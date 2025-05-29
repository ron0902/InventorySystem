<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inspection and Acceptance Report</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 10mm 15mm 10mm;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background: #fff;
        }
        .inspection-container {
            width: 200mm;
            min-height: 200mm;
            margin: 40px auto;
            border: 1.5px solid #000;
            padding: 0;
            box-sizing: border-box;
            background: #fff;
        }
        .inspection-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 18px 10px 5px 10px;
        }
        .inspection-header .main-title {
            font-size: 22px;
            font-weight: normal;
            letter-spacing: 1px;
        }
        .inspection-header .sub-title {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
        }
        .inspection-header .agency {
            font-size: 15px;
            font-weight: bold;
            margin-top: 2px;
        }
        .inspection-meta {
            width: 100%;
            font-size: 14px;
            margin: 0;
            border-bottom: 1.5px solid #000;
        }
        .inspection-meta td {
            padding: 4px 8px;
            border: none;
        }
        .inspection-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .inspection-table th, .inspection-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }
        .inspection-table th {
            background: #fff;
            font-weight: bold;
        }
        .inspection-table td {
            height: 22px;
        }
        .purpose-section {
            border-top: 1.5px solid #000;
            padding: 6px 0 0 0;
            font-size: 14px;
        }
        .purpose-section label {
            font-weight: bold;
        }
        .inspection-bottom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 0;
        }
        .inspection-bottom-table td, .inspection-bottom-table th {
            border: 1px solid #000;
            vertical-align: top;
            padding: 6px 8px;
        }
        .inspection-checkbox {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1.5px solid #000;
            margin-right: 6px;
            vertical-align: middle;
        }
        .signatories {
            width: 100%;
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
        }
        .signatories td {
            padding: 24px 4px 2px 4px;
            border: none;
        }
        .signatories .chairman {
            font-weight: bold;
        }
        .signatories .role {
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="inspection-container">
    <div class="inspection-header">
        <div class="main-title">INSPECTION AND ACCEPTANCE REPORT</div>
        <div class="sub-title">DEPARTMENT OF EDUCATION</div>
        <div class="agency">Agency</div>
    </div>
    <table class="inspection-meta">
        <tr>
            <td style="width:40%;">Supplier ____________________________</td>
            <td style="width:20%;">IAR No. ____________</td>
        </tr>
        <tr>
            <td>PO NO. ____________________________</td>
            <td>Date: ____________</td>
            <td>Invoice No. ____________</td>
        </tr>
    </table>
    <table class="inspection-table">
        <tr>
            <th style="width:12%;">Stock No.</th>
            <th style="width:10%;">Unit</th>
            <th>Description</th>
            <th style="width:18%;">Quantity</th>
        </tr>
        <tr>
            <td>01</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
        <tr>
            <td>02</td>
            <td>#REF!</td>
            <td>#REF!</td>
            <td>#REF!</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:center;">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxNothing Followsxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</td>
        </tr>
        <!-- Add more empty rows for spacing -->
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
    </table>
    <div class="purpose-section">
        <label>Purpose :</label>
    </div>
    <table class="inspection-bottom-table">
        <tr>
            <td style="width:55%;">
                <div style="margin-bottom:6px;"><b>Date Inspected:</b></div>
                <div style="height:40px;">inspected, verified are found in order as to quantity and specified</div>
            </td>
            <td style="width:25%;">
                <div style="margin-bottom:6px;"><b>Date Received:</b></div>
                <div><span class="inspection-checkbox"></span>Complete</div>
                <div><span class="inspection-checkbox"></span>Quantity</div>
            </td>
        </tr>
    </table>
    <table class="signatories">
        <tr>
            <td>Member</td>
            <td>Member</td>
            <td class="chairman">CHAIRMAN</td>
        </tr>
        <tr>
            <td class="role"></td>
            <td class="role"></td>
            <td class="role">INSPECTION OFFICER/Inspection Committe</td>
        </tr>
    </table>
</div>
</body>
</html>