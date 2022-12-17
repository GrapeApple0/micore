<?php
session_start();
?>
<div class="d-flex flex-column flex-row flex-items-center">
	<div class="col-12 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
		<label class="primary-text">リダイレクトチェッカー</label>
		<p class="primary-text">Bitlyや04.siなどの短縮URLのリダイレクト先を表示します。</p>
	</div>
</div>
<?php if (filter_input(INPUT_POST, "status") == "") { ?>
	<form action="" method="post">
		<div class="d-flex flex-column flex-row flex-items-center">
			<div class="col-12 d-flex flex-justify-center flex-items-center pl-md-4">
				<input class="col-8 textbox" type="url" id="url" name="url" placeholder="Enta URL">
				<input type="hidden" name="status" value="send" required>
				<button class="btn secondary-color primary-button" type="submit">Senda</button>
			</div>
		</div>
	</form>
	<?php if (htmlspecialchars(ltrim($url, "tools/redirect/")) != "") { ?>
		<div class="d-flex flex-column flex-row flex-items-center">
			<label class="primary-text h4 text-normal">Result:</label>
			<div class="col-8 d-flex flex-justify-center flex-items-center pl-md-4">
				<?php
				$rurl = ltrim($url, "tools/redirect/");
				if (preg_match('/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/', $rurl)) {
					$req_url = "https://mico.re/api/redirect.php";
					$postdata = array("url" => $rurl);
					$ch = curl_init($req_url);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
					$result_json = curl_exec($ch);
					$result = json_decode($result_json, true);
					curl_close($ch);
				?>
					<?php if ($result["status"] == "2") { ?>
						<label class="primary-text"><a class="link" href="<?php echo $rurl ?>"><?php echo $rurl ?></a>はリダイレクトしないです。</label>
					<?php } else { ?>
						<label class="primary-text text-normal"><a class="link" href="<?php echo $rurl ?>"><?php echo $rurl ?></a>は<a class="link" href="<?php echo $result["result"]; ?>"><?php echo $result["result"]; ?></a>へリダイレクトします。</label>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
<?php } else if ($_SESSION["resulted"] == false) { ?>
	<?php
	$_SESSION["resulted"] = true;
	$rurl = htmlspecialchars(filter_input(INPUT_POST, "url"));
	header("Location:" . "/tools/redirect/" . htmlspecialchars($rurl));
	exit;
	?>
<?php } ?>