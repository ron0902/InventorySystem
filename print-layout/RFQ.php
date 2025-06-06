<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request for Quotation</title>
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm 20mm 15mm;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .rfq-outer {
            width: 100vw;
            min-height: 100vh;
            display: flex;
            align-items: flex-start; /* Align to top */
            justify-content: center;
        }
        .rfq-container {
            width: 210mm;
            min-height: 200mm;
            margin: 40px auto 0 auto; /* Add top margin */
            border: 1px solid #000;
            padding: 20px 15px;
            box-sizing: border-box;
            background: #fff;
        }
        .rfq-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .rfq-header img {
            height: 70px;
            vertical-align: middle;
        }
        .rfq-title {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
        .rfq-subtitle {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .rfq-meta, .rfq-meta td {
            font-size: 13px;
            border: none;
            padding: 2px 4px;
        }
        .rfq-company {
            margin: 15px 0 5px 0;
            font-size: 13px;
        }
        .rfq-notes {
            font-size: 12px;
            margin-bottom: 10px;
        }
        .rfq-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .rfq-table th, .rfq-table td {
            border: 1px solid #000;
            font-size: 12px;
            padding: 4px 2px;
            text-align: center;
        }
        .rfq-table th {
            background: #f0f0f0;
        }
        .rfq-signature {
            margin-top: 20px;
            font-size: 12px;
        }
        .rfq-signature td {
            border: none;
            padding: 8px 4px;
        }
        .rfq-footer {
            margin-top: 20px;
            font-size: 12px;
        }
        .rfq-footer td {
            border: none;
            padding: 2px 4px;
        }
        @media print {
            body, .rfq-outer, .rfq-container {
                box-shadow: none;
                border: none;
                background: #fff;
            }
            .rfq-outer {
                display: block;
            }
        }
    </style>
</head>
<body>
<div class="rfq-outer">
<div class="rfq-container">
   <div class="rfq-header">
    <table style="width:100%; border:none;">
        <tr>
            <td style="width:15%; text-align:left; border:none;">
                <img src="../images/Picture2.png" alt="DepEd Logo" style="height:70px;">
            </td>
            <td style="width:70%; text-align:center; border:none;">
                <div>Region XII</div>
                <div>NEW SOCSARGEN NATIONAL HIGH SCHOOL</div>
                <div>General Santos City</div>
                <div class="rfq-title">DEPARTMENT OF EDUCATION</div>
                <div class="rfq-title">REQUEST FOR QUOTATION</div>
            </td>
            <td style="width:15%; text-align:right; border:none;">
                <img src="../images/Picture1.png" alt="School Logo" style="height:70px;">
            </td>
        </tr>
    </table>
</div>
    <table class="rfq-meta" style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="width:60%;">Date: ____________________</td>
            <td style="width:40%;">Quotation No.: ____________________</td>
        </tr>
    </table>
    <div class="rfq-company">
        <div>(Company Name) ____________________________________________</div>
        <div>(Address) _________________________________________________</div>
    </div>
    <div class="rfq-notes">
        Please quote your lowest price on the item/s listed below, subject to the General Conditions on the last page, stating the shortest time of delivery and submit your quotation duly signed by your representative not later than _____________________.<br>
        <b>NOTE:</b>
        <ol style="margin:0 0 0 20px; padding:0;">
            <li>ALL ENTRIES MUST BE TYPEWRITTEN.</li>
            <li>DELIVERY PERIOD WITHIN __________ CALENDAR DAYS.</li>
            <li>WARRANTY SHALL BE FOR A PERIOD OF SIX (6) MONTHS FOR SUPPLIES & MATERIALS, ONE (1) YEAR FOR EQUIPMENT, FROM DATE OF ACCEPTANCE BY THE PROCURING ENTITY.</li>
            <li>PRICE VALIDITY SHALL BE FOR A PERIOD OF __________ CALENDAR DAYS.</li>
            <li>PHILGEPS REGISTRATION CERTIFICATE SHALL BE ATTACHED UPON SUBMISSION OF THE QUOTATION.</li>
            <li>BIDDERS SHALL SUBMIT ORIGINAL BROCHURES SHOWING CERTIFICATIONS OF THE PRODUCT BEING OFFERED.</li>
        </ol>
    </div>
    <table class="rfq-table">
        <thead>
        <tr>
            <th>No.</th>
            <th>Item Description</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Approved Budget for the Contract</th>
            <th>Brand</th>
            <th>Bid Amount</th>
        </tr>
        </thead>
        <tbody>
    <tr style="height:20px;">
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    </tr>
        </tbody>
    </table>
    </table>
    <!-- INSERT THIS BLOCK BELOW -->
    <table style="width:100%; margin-top:18px; margin-bottom:8px; border:none;">
        <tr>
<td colspan="2" style="vertical-align:top; border:none;">
    <table style="width:100%; margin-top:0; margin-bottom:0; border:none;">
    <tr>
    <td style="width:55%; border:none; vertical-align:top;"></td>
    <td style="width:45%; border:none; vertical-align:top;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="font-size:13px; border:none; padding:2px 0; text-align:left; width:60%;">Brand and Model</td>
                <td style="border:none; padding:2px 0; width:40%;">
                    <div style="border-bottom:1px solid #000; width:100%;">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td style="font-size:13px; border:none; padding:2px 0; text-align:left;">Delivery Period</td>
                <td style="border:none; padding:2px 0;">
                    <div style="border-bottom:1px solid #000; width:100%;">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td style="font-size:13px; border:none; padding:2px 0; text-align:left;">Warranty</td>
                <td style="border:none; padding:2px 0;">
                    <div style="border-bottom:1px solid #000; width:100%;">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td style="font-size:13px; border:none; padding:2px 0; text-align:left;">Price Validity</td>
                <td style="border:none; padding:2px 0;">
                    <div style="border-bottom:1px solid #000; width:100%;">&nbsp;</div>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
</td>
    </tr>
        <tr>
   <td colspan="2" style="border:none; padding-bottom:2px;">
    <div style="font-weight:bold; text-decoration:underline;">New Society National High School</div>
    <div style="text-align:left; margin-left:45px;">
        <span style="font-style:italic; font-size:12px;">Name of the Procuring Entity</span>
    </div>
</td>
</tr>
<tr>
    <td colspan="2" style="border:none; text-align:left; font-size:13px; padding-bottom:8px;">
        After having carefully read and accepted your General Conditions, I/We quote you on the item at prices noted above.
    </td>
</tr>
    </table>
    <div class="rfq-footer" style="margin-top:30px;">
    <table style="width:100%; border:none; font-size:12px;">
        <tr>
            <!-- LEFT: Reference box -->
            <td style="width:38%; vertical-align:top; border:none;">
                <table style="border-collapse:collapse; width:70%;">
                    <tr>
                        <td colspan="2" style="border:1px solid #000; text-align:left; font-weight:bold; padding:2px 6px;">REFERENCE</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000; padding:2px 6px;">PR #</td>
                        <td style="border:1px solid #000; padding:2px 6px;">DATE</td>
                    </tr>
                </table>
                <div style="margin-top:8px;">
                    <span style="font-size:11px; font-style:italic;">NSNHS-GSC/BAC</span><br>
                    <span style="font-size:11px;">PHILGEPS REF. No.</span>
                    <span style="border-bottom:1px solid #000; display:inline-block; width:110px; vertical-align:middle;"></span>
                </div>
            </td>
            <!-- RIGHT: Signature lines -->
            <td style="width:62%; border:none; vertical-align:top;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="border-bottom:1px solid #000; width:100%;"></td>
                    </tr>
                    <tr>
                        <td style="text-align:center; font-size:12px; padding:0 0 4px 0;">Printed Name/Signature</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid #000; width:100%;"></td>
                    </tr>
                    <tr>
                        <td style="text-align:center; font-size:12px; padding:0 0 4px 0;">Tel. No./Cellphone No. email address</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid #000; width:100%;"></td>
                    </tr>
                    <tr>
                        <td style="text-align:center; font-size:12px; padding:0 0 4px 0;">Date</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <!-- Bottom row: Date Published and Closing Date -->
            <td style="border:none; padding-top:10px;" colspan="2">
                <span style="font-size:12px;">Date Published:</span>
                <span style="border-bottom:1px solid #000; display:inline-block; width:120px; vertical-align:middle;"></span>
                &nbsp;&nbsp;
                <span style="font-size:12px;">Closing Date:</span>
                <span style="border-bottom:1px solid #000; display:inline-block; width:120px; vertical-align:middle;"></span>
            </td>
        </tr>
    </table>
</div>
</div>
</body>
</html>