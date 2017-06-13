<?php
$dr_messages = $this->get('dr_messages');
$dr_categories = $this->get('dr_categories');

$categories = array_keys($dr_messages);
?>
<html>
<head>
    <meta charset="UTF-8"/>
</head>
<body style="font-family: Helvetica, Arial, Sans-serif;font-weight: 100; background: #41424e;">
	<div style="width: 600px;margin-left: auto; margin-right: auto;background: white;">

		<h1 style="background: #2f2f39; color: white; padding: 8px 8px 8px 8px; font-size: 28px;"><?= _('Votre récapitulatif journalier'); ?></h1>

		<div style="padding: 0px 8px 8px 8px; font-size: 15px; color: #222222;">
			<?php foreach ($categories as $categ): ?>
				<?php if(!empty($dr_categories[$categ])): ?>
					<h2><?= sprintf(_($dr_categories[$categ]),count($dr_messages[$categ])); ?></h2>
				<?php endif; ?>

				<div style="border-bottom: 1px solid #333333; padding-bottom:8px;">
					<?php foreach ($dr_messages[$categ] as $m): ?>
						<?= $m; ?>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
			<br/>
			<p style="font-size:11px;">Cet email a été généré de manière automatique. Si vous n'êtes pas concerné par son contenu, merci de l'ignorer
				et d'informer l'organisme de l'erreur.<br />
				Merci de ne pas répondre à cet e-mail, tous les messages envoyés à cette adresse seront ignorés.</p>
		</div>
	</div>
</body>
