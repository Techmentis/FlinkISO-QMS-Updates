<?php
$emailTitle = isset($emailTitle) && $emailTitle !== '' ? $emailTitle : 'FlinkISO Notification';
$emailPreheader = isset($emailPreheader) ? $emailPreheader : '';
$environment = isset($environment) ? $environment : null;
$brandUrl = isset($app_url) ? $app_url : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($emailTitle); ?></title>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5; font-family:Arial, Helvetica, sans-serif; color:#333333;">
<?php if ($emailPreheader !== ''): ?>
<div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;"><?php echo h($emailPreheader); ?></div>
<?php endif; ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; background-color:#f5f5f5;">
    <tr>
        <td align="center" style="padding:30px 15px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border:1px solid #e5e5e5;">
                <tr>
                    <td style="padding:24px 30px; background-color:#1769aa; color:#ffffff;">
                        <div style="font-size:24px; line-height:1.2; font-weight:bold;">
                            <?php if ($brandUrl): ?><a href="<?php echo h($brandUrl); ?>" style="color:#ffffff; text-decoration:none;"><?php endif; ?>
                            FlinkISO
                            <?php if ($brandUrl): ?></a><?php endif; ?>
                        </div>
                        <div style="font-size:14px; line-height:1.4; margin-top:5px;">Quality Management Software<?php if ($environment): ?> &middot; <?php echo h($environment); ?><?php endif; ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px; font-size:15px; line-height:1.6;">
                        <h1 style="margin:0 0 24px 0; color:#1769aa; font-size:22px; line-height:1.35; font-weight:600;"><?php echo h($emailTitle); ?></h1>
