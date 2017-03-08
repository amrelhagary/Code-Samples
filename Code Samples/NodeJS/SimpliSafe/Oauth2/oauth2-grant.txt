/**
 * coding style
 */
var oauth2orize = require('oauth2orize');
var passport    = require('passport');
var util        = require('./util');
var db          = require('../db/mongoose');
var TokenModel  = require('../db/mongoose').TokenModel;
var Token       = require('../db/tokens');
var CodeModel   = require('../db/mongoose').CodeModel;
var clients     = require('../db/clients');
var config      = require('../conf/config');
var Logger      = require('./log').appLog(module);
var User        = require('../db/drupal-users').User;
var validators  = require('./validators');
var client      = require('../db/clients');
var crypto      = require('crypto');

// create OAuth 2.0 server
var server = oauth2orize.createServer();

server.grant(oauth2orize.grant.code(function(client, redirectURI, userId, ares, done) {
    var code = util.uid(16);
    var codeModel = new CodeModel({
        value: code,
        clientId: client.clientId,
        userId: userId
    });
    return codeModel.save(function(err, result) {
        if (err) { return done(err); }

        // this allow listening to events belong to this user in event-notification: handleSocketEvent
        return User.addUserToEventTracking(userId, function(err, result) {
            if(err) {
                return done(err);
            }
            else{
                return done(null, code);
            }
        });
    });
}));

// exchange code with token
server.exchange(oauth2orize.exchange.code(function(client, code, redirectURI, done) {
    db.CodeModel.findOne({value: code}, function(err, code) {

        if (err) { return done(err); }

        if(!client) {
            return done(new Error('client not exist for authorization code : ' + code));
        }

        if(!code) {
            Logger.error('authorization code not exist : ' + code);
            return done(new Error('authorization code not exist'));
        }

        if (client.clientId !== code.clientId) { return done(null, false); }
        //if (redirectURI !== client.redirectURI) { return done(null, false); }

        var tokenObj = {userId: code.userId,clientId:  code.clientId,scope: client.scope};
        Token.generateToken(tokenObj, function(err, newAccessToken){
            if(err) { return done(err); }

            return done(null, newAccessToken.accessToken, newAccessToken.refreshToken , { 'expires_in': config.oauth.tokenLife });
        });
    });
}));

server.exchange(oauth2orize.exchange.refreshToken(function (client, refreshToken, scope, done) {
    Token.findByRefreshToken(refreshToken, function (err, token) {
        if(err) { return done(err, null);}

        if(!token) {
            Logger.error('refresh token not exist : ' + refreshToken);
            return done(new Error('refresh token not exist'));
        }

        var tokenObj = {userId: token.userId,clientId:  token.clientId,scope: client.scope};
        Token.generateToken(tokenObj, function(err, newAccessToken){
            if(err) { return done(err);}

            return done(null, newAccessToken.accessToken, newAccessToken.refreshToken , { 'expires_in': config.oauth.tokenLife });
        });
    });
}));

//TODO this should moved to token model
exports.authBearer = function(accessToken , done ) {

    TokenModel.findOne({ accessToken: accessToken }, function(err, token) {

        if (err) {
            Logger.error('BearerStrategy TokenModel.findOne failed: ' + err.message);
            return done(err);
        }


        if (!token) {
            Logger.warn('BearerStrategy accessToken not found');
            return done(new Error('Invalid Token'));
        }

        if ( (Date.now() - token.created.getTime() ) / 1000 > config.oauth.tokenLife) {
            Logger.warn('BearerStrategy token expired');
            return done(new Error('Token expired'));
        }
        var info = {scope: [token.scope]};
        token.info = info;
        return done(null, token);
    });
}

/**
 * get Authorization code for the client
 * @param req
 * @param res
 */
exports.getAuthorizationCode = function(req, res) {
    validators.doValidators([validators.validateAccessToken], req, res, function (req, res) {
        var clientId = req.param('client_id');
        var redirectURI = req.param('redirect_uri');

        clients.findByClientId(clientId, function (err, client) {
            
            try
            {
                if (client.redirectURI !== decodeURIComponent(redirectURI)) {
                    throw new Error("client not found");
                }
            }
            catch(err) {
                return res.status(500).json({
                    error: err.message
                });
            }

            var userId = req.user_token.token.userId;

            var code = util.uid(16);
            var codeModel = new CodeModel({
                value: code,
                clientId: client.clientId,
                userId: userId
            });

            return codeModel.save(function (err, result) {
                if (err) {
                    return res.status(500).json({"error": err.message});
                }

                // this allow listening to events belong to this user in event-notification: handleSocketEvent
                return User.addUserToEventTracking(userId, function (err, result) {

                    if (err) {
                        return res.status(500).json({"error": err.message});
                    }
                    else {
                        return res.status(200).json({"authorization_code": code, redirect_uri: client.redirectURI});
                    }
                });
            });
        });
    });
};

/**
 * Check Access Token in the request
 * access token is stored in browser cookie
 * return client name to be displayed in dialog form
 * @param req
 * @param res
 */
exports.checkAccessToken = function (req, res) {
    validators.doValidators([validators.validateAccessToken], req, res, function (req, res) {
        var clientId = req.param('client_id');
        var redirectURI = req.param('redirect_uri');

        clients.findByClientId(clientId, function (err, client) {

            try
            {
                if (client.redirectURI !== decodeURIComponent(redirectURI)) {
                    throw new Error("client not found");
                }
            }
            catch(err) {
                return res.status(500).json({
                    error: err.message
                });
            }

            return res.status(200).json({
                client: {name: client.clientName}
            });
        });
    });
};

/**
 * Check the user locally
 * this used for login page
 * @param req
 * @param res
 */
exports.localAuthorization = function (req, res) {

    var clientId    = req.param('client_id') || null;
    var username    = req.param('username')  || null;
    var password    = req.param('password')  || null;
    var scope       = req.param('scope')     || null;
    var redirectURI = req.param('redirect_uri') || null;

    if(!clientId || !username || !password || !scope || !redirectURI) {
        res.status(400).json({error: "Invalid Request"});
    }

    var func = '';
    if(username.indexOf('@') === -1) {
        func = 'findByUsername'
    }
    else{
        func = 'findByEmail';
    }

    User[func](username, function(err, user){

        if (err) {
            return res.status(500).json({ error: err.message });
        }

        if (!user.userId || user.password != crypto.createHash('md5').update(password).digest("hex")) {
            return res.status(500).json({ error: "invalid username or password" });
        }

        client.findByClientId(clientId, function(err, client) {

            if(client.redirectURI !== decodeURIComponent(redirectURI)) {
                return res.status(401).json({ error: 'client not found' });
            }

            var tokenObj = {userId: user.userId,clientId:  clientId, scope: client.scope};
            Token.generateToken(tokenObj, function(err, newAccessToken){
                if(err) { return res.status(500).json({ error: err.message });}

                return res.status(200).json({
                    access_token: newAccessToken.accessToken,
                    expires_in: config.oauth.tokenLife,
                    client: {
                        name: client.clientName
                    }
                });
            });

        });

    });
};

exports.token = [
    passport.authenticate(['oauth2-client-password'], { session: false }),
    server.token(),
    server.errorHandler()
]