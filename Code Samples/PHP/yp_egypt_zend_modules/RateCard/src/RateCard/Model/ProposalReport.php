<?php
namespace RateCard\Model;


class MyPDF extends \TCPDF
{
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'yellowmedia_logo.png';
        $this->Image($image_file, 0, 10, 0, 0, 'PNG', '', 'M', false, 300, 'C', false, false, 0, false, false, false);

        $this->SetAutoPageBreak(true);
        // Set font
        $this->setX(50);
        $this->SetY(30);
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 0, 'Media Plan Proposal', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);

        $style4 = array('L' => 0,
            'T' => array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => '20,10', 'phase' => 10, 'color' => array(255, 242, 0)),
            'R' => array('width' => 0.50, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 242, 0)),
            'B' => array('width' => 0.75, 'cap' => 'square', 'join' => 'miter', 'dash' => '30,10,5,10'));

        $this->Rect(0,$this->getPageHeight() - 10, $this->getPageWidth(), 20, 'DF', $style4, array(255, 242, 0));
    }
}

class ProposalReport
{

    public function exportPDF($invoice,$save = false)
    {
        $pdf = new MyPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Yellow Pages');
        $pdf->SetTitle('Proposal');
        $pdf->SetSubject('Client: '.$invoice['client_name']);

        $fontName = 'helvetica';

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);


        $pdf->setHeaderFont(Array($fontName, '', 10));
        $pdf->setFooterFont(Array($fontName, '', 9));

        $monospaceFont = 'courier';

        $pdf->SetDefaultMonospacedFont($monospaceFont);


        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);


        $pdf->SetAutoPageBreak(TRUE, 25);


        $pdf->setImageScale(1.25);
        $pdf->setFontSubsetting(true);

        $pdf->SetFont('dejavusans', '', 14, '', true);
        $pdf->AddPage();

        // set only header in the first page
        $pdf->setPrintHeader(false);

        // prevent content overlapping the footer
        $pdf->SetAutoPageBreak(TRUE, 25);

        $style = <<<__css
<style>
.client, .header, .closure
{
    font-size: 12px;
}
.regards
{
    font-size:13px;
}
table.platform {
    width: 100%;
}

table.platform tr {
    line-height: 30px;
}
table.platform th {
    background-color: #bdbcbc;
    font-size: 12px;
}
table.platform td {
    background-color: #dedede;
}

table.platform .body{
    font-size: 10px;
}

table.platform .footer {
    background-color: #bdbcbc;
    font-size: 12px;
}
</style>
__css;


        $client = '<br/><br/><br/><br/><table style=\"width:100%\" class="client">';
        $client .='<tr><td style="width:15%">Client:</td><td style="width:85%">'.$invoice['client_name'].'</td></tr>';
        $client .='<tr><td style="width:15%">Mobile:</td><td style="width:85%">'.$invoice['client_mobile'].'</td></tr>';
        $client .='<tr><td style="width:15%">E-Mail:</td><td style="width:85%">'.$invoice['client_email'].'</td></tr>';
        $client .='<tr><td style="width:15%">Date:</td><td style="width:85%">'.date('d/M/Y h:i A',strtotime($invoice['created_at'])).'</td>';
        $client .= '</tr></table>';

        $header = '<br/><br/><p class="header">Thanks for your interest in Yellow Media, please find below our media plan that is tailored
according to your business objectives.</p>';


        $table = '<table class="platform" cellspacing="4" cellpadding="1">';
        $table .= '<tr>
        <th align="center" width="15%">Platform</th>
        <th align="center" width="30%">Item</th>
        <th align="center" width="5%">Qty</th>
        <th align="center" width="10%" style="font-size:13px">Amount</th>
        <th align="center" width="10%" style="font-size:13px">Discount</th>
        <th align="center" width="30%">Comment</th>
        </tr>';
        $platformDiscounts = array();
        $totalPaymentAmount = 0;
        $saving = 0;
        foreach($invoice['platform'] as $platform){
            foreach($platform['service'] as $items){
                $serviceTitle = (!empty($platform['service']['title'])) ? $platform['service']['title'].' - ': '';
                if(is_array($items))
                {
                    $table .= '<tr>';
                    foreach($items as $id => $item){
                        $table .= '<td class="body" align="center" width="15%">'.$platform['title'].'</td>';
                        $table .= '<td class="body" align="center" width="30%">'.$serviceTitle.$item['package_name'].'</td>';
                        $table .= '<td class="body" align="center" width="5%">'.$item['quantity'].'</td>';
                        // Reformat number to set comma decimal separator for number above thousand
                        $table .= '<td class="body" align="center" width="10%">'.number_format($item['amount']*$item['quantity']).'</td>';
                        $table .= '<td class="body" align="center" width="10%">'.number_format($item['quantity'] * $item['amount'] * $platform['discount'] / 100) .'</td>';
                        $table .= '<td class="body" align="center" width="30%">'.$item['comment'].'</td>';
                    }
                    $table .= '</tr>';
                }
            }

            // total payments for all platforms
            $totalPaymentAmount += $item['amount'] * $item['quantity'];

            // calculate discounts for platforms having discounts
            if($platform['discount'] > 0){
                $platformDiscountAmount = !empty($platformDiscounts[$platform['id']]) ? $platformDiscounts[$platform['id']]['total_amount'] : $platformDiscounts[$platform['id']]['total_amount'] = 0;
                $platformDiscounts[$platform['id']]['total_amount'] = $platformDiscountAmount + ($item['quantity'] * $item['amount']);
                $platformDiscounts[$platform['id']]['discount'] = $platform['discount'] ;
            }
        }


        // calculate total saving for all platforms
        foreach($platformDiscounts as $platformDiscount)
        {
            $saving += $platformDiscount['total_amount'] * ($platformDiscount['discount'] / 100);
        }

        $table .= '<tr><td class="footer" colspan="2" align="center">Total Investment</td><td class="footer" colspan="4" align="center">L.E '.number_format($totalPaymentAmount).'</td></tr>';
        $table .= '<tr><td class="footer" colspan="2" align="center">You Save</td><td class="footer" colspan="4" align="center">L.E '.number_format($saving).'</td></tr>';
        $table .= '<tr><td class="footer" colspan="2" align="center">Net Price</td><td class="footer" colspan="4" align="center">L.E '.number_format($totalPaymentAmount - $saving ).'</td></tr>';
        $table .= '</table>';

        // set indent width for list
        $pdf->setListIndentWidth(0);
        $notes = '<br/><span><ul style="line-height: 11em;list-style-type: none;font-size:12px;"><li><sup>*</sup>&nbsp;<b>Payment terms:</b> For one year campaign durations: 50% downpayment with the remaining balance in 6 months or less. For all campaign durations ranging between 1 and 10 months: 50% downpayment with the remaining balance payable within the first half of the campaign’s overall duration</li>';
        $notes .= sprintf('<li><sup>*</sup>&nbsp;This offer is valid for 1 week from <b> %s </b></li></ul></span>',date('d/M/Y'));

        $closure = '<p class="closure" style="margin-bottom: 10em">Thanks for reading this proposal, please contact your Media Consultant for any further information.</p>';
        $regards = '<br/><p class="regards"><span>Best Regards,</span><br/><span><b>Yellow Media</b></span></p>';


        $content = $style . $client . $header ."<br/><br/>". $table . $notes . $closure . $regards;
        $pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);


// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        if($save == true){
            $file = '/tmp/proposal_'.strtotime('now').'_'.rand().'.pdf';
            $pdf->Output($file, 'F');
            return $file;
        }else{
            $pdf->Output('Proposal.pdf','I');
        }

    }

}