<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI Support Escalation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 24px;
            color: #ffffff;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .content {
            padding: 32px;
        }
        .meta-box {
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 14px;
        }
        .meta-row:last-child {
            border-bottom: none;
        }
        .meta-label {
            font-weight: 600;
            color: #64748b;
        }
        .meta-value {
            font-weight: 700;
            color: #0f172a;
        }
        .message-section {
            font-size: 15px;
            line-height: 1.6;
            color: #334155;
            background-color: #fafafa;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            border-radius: 4px;
            margin-top: 16px;
            white-space: pre-wrap;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Support Escalation request</h1>
        </div>
        <div class="content">
            <p style="margin-top: 0; font-size: 16px;">An escalation request has been submitted via the AI Help Center.</p>
            
            <div class="meta-box">
                <div class="meta-row">
                    <span class="meta-label">User Name:</span>
                    <span class="meta-value">{{ $user->name }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Email:</span>
                    <span class="meta-value">{{ $user->email }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Escalation Subject:</span>
                    <span class="meta-value">{{ $subjectLine }}</span>
                </div>
                @if ($orderNumber)
                <div class="meta-row">
                    <span class="meta-label">Associated Order:</span>
                    <span class="meta-value">#{{ $orderNumber }}</span>
                </div>
                @endif
            </div>

            <h3 style="margin-bottom: 8px; color: #0f172a;">User Message:</h3>
            <div class="message-section">{{ $description }}</div>
        </div>
        <div class="footer">
            Sent automatically by PlexusBiz Automate Support System.
        </div>
    </div>
</body>
</html>
