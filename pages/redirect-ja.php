<?php
session_start();
?>
<div class="d-flex flex-column flex-row flex-items-center">
	<div class="col-12 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
		<label>リダイレクトチェッカー</label>
		<p>Bitlyや04.siなどの短縮URLのリダイレクト先を表示します</p>
	</div>
</div>
<?php if (filter_input(INPUT_POST, "status") == "" && $_SESSION["resulted"] == null) { ?>
	<form action="" method="post">
		<div class="d-flex flex-column flex-row flex-items-center">
			<div class="col-12 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
				<input class="form-control col-8" type="url" id="url" name="url" placeholder="URLを入力">
				<input type="hidden" name="status" value="send" required>
				<button class="btn" type="submit">送信</button>
			</div>
		</div>
	</form>
<?php } else if ($_SESSION["resulted"]) { ?>
	<?php
	$_SESSION["resulted"] = null;
	?>
	<div class="d-flex flex-column flex-row flex-items-center">
		<div class="col-8 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
			<?php if ($_SESSION["status"] == "2") { ?>
				<p><?php echo $_SESSION["result"]["url"]; ?>はリダイレクトしないです。</p>
			<?php } else { ?>
				<p><?php echo $_SESSION["result"]["url"]; ?>のリダイレクト先は<a class="link" href="<?php echo $_SESSION["result"]["redirect"]; ?>"><?php echo $_SESSION["result"]["redirect"]; ?></a>です</p>
			<?php } ?>
		</div>
	</div>
<?php } else { ?>
	<?php
	$_SESSION["resulted"] = true;
	$url = htmlspecialchars(filter_input(INPUT_POST, "url"));
	if ($url == "" || !preg_match('/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/', $url)) {
		$_SESSION["status"] = "1";
		$_SESSION["result"] = "Error: URL is not found or not match regex.";
		header("location:" . htmlspecialchars($_SERVER['REQUEST_URI']));
	} else {
		$_SESSION["status"] = "0";
		$req_url = "https://mico.re/api/redirect.php";

		$postdata = array("url" => $url);
		$ch = curl_init($req_url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$result_json = curl_exec($ch);
		$result = json_decode($result_json, true);
		curl_close($ch);
		$_SESSION["result"] = [];
		$_SESSION["result"]["redirect"] = $result["result"];
		$_SESSION["result"]["url"] = $url;
		$_SESSION["status"] = $result["status"];
		header("location:" . htmlspecialchars($_SERVER['REQUEST_URI']) . "/");
		exit;
	}
	?>
<?php } ?>