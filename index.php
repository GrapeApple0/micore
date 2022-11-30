<?php
session_start();
$url = rtrim(ltrim(htmlspecialchars($_SERVER["REQUEST_URI"], ENT_QUOTES, 'UTF-8'), "/"), "/");
$settings_json = file_get_contents(__DIR__ . '/config/settings.json');
if ($settings_json == false) {
	http_response_code(500);
	echo "'settings.json' is not found.";
	exit;
}
$settings = json_decode($settings_json, true);
$theme_json = file_get_contents(__DIR__ . '/config/theme.json');
if ($theme_json == false) {
	http_response_code(500);
	echo "'theme.json' is not found.";
	exit;
}
$theme = json_decode($theme_json, true);
$lang = filter_input(INPUT_COOKIE, "lang");
if ($lang == "") {
	$lang = "ja";
	setcookie("lang", "ja");
}
$tools_json = file_get_contents(__DIR__ . "/config/tools-$lang.json");
if ($theme_json == false) {
	http_response_code(500);
	echo "'tools-ja.json' is not found.";
	exit;
}
$tools = json_decode($tools_json, true);
?>
<!DOCTYPE html>
<html lang="en">

<head prefix="og: https://ogp.me/ns#">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta property="og:url" content="https://<?php echo $settings["domain"] . htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
	<meta property="og:image" content="https://<?php echo $settings["domain"] ?>/favicon.ico" />
	<?php if (explode("/", $url)[0] == "tools") { ?>
		<meta property="og:title" content="<?php echo $tools[explode("/", $url)[1]]["title"] ?>" />
		<meta property="og:description" content="<?php echo $tools[explode("/", $url)[1]]["description"] ?>" />
	<?php } else { ?>
		<meta property="og:title" content="micore" />
		<meta property="og:description" content="いろんなツールを公開してます。" />
	<?php } ?>
	<link rel="shortcut icon" href="<?php echo $settings["favicon"]; ?>" type="image/x-icon">
	<link href="https://unpkg.com/@primer/css@^20.2.4/dist/primer.css" rel="stylesheet" />
	<title><?php echo $settings["name"]; ?></title>
	<style>
		body {
			display: flex;
			flex-flow: column;
			min-height: 100vh;
		}

		main {
			flex: 1;
		}

		.primary-color {
			background-color: <?php echo $theme["primary-color"] ?>;
		}

		.secondary-color {
			background-color: <?php echo $theme["secondary-color"] ?>;
		}

		.primary-text {
			color: <?php echo $theme["primary-text"] ?>;
		}

		.secondary-text {
			color: <?php echo $theme["secondary-text"] ?>;
		}

		.primary-button {
			background-color: <?php echo $theme["primary-button"] ?>;
			color: <?php echo $theme["secondary-color"] ?>;
		}

		.primary-button:hover {
			background-color: <?php echo $theme["primary-button-hover"] ?>;
			color: <?php echo $theme["secondary-color"] ?>;
		}

		.link {
			color: <?php echo $theme["link"] ?>;
		}
	</style>
</head>

<body class="secondary-color">
	<div class="Header secondary-color">
		<div class="Header-item">
			<a href="/" class="Header-link f4 d-flex flex-items-center">
				<span class="secondary-text"><?php echo $settings["name"] ?></span>
			</a>
		</div>
		<div class="Header-item Header-item--full"></div>
		<div class="Header-item mr-1">
			<a class="link" href="/about">About</a>
		</div>
		<div class="Header-item mr-1">
			<a class="link" href="/donate">Donate</a>
		</div>
	</div>
	<main>
		<?php if ($url == "") { ?>
			<div class="container-lg clearfix">
				<?php foreach ($tools as $key => $value) { ?>
					<div class="col-sm-12 col-md-6 col-xl-4 float-left p-4">
						<div class="Box color-shadow-small">
							<div class="Box-row">
								<h3 class="m-0"><?php echo $value["title"] ?></h3>
							</div>
							<div class="Box-row">
								<p class="mb-0 color-fg-muted">
									<?php echo $value["description"] ?>
								</p>
							</div>
							<div class="Box-row">
								<a name="Create an organization" class="btn btn-block primary-button" href="<?php echo $value["url"] ?>">
									使ってみる
								</a>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } else if (explode("/", $url)[0] == "donate") { ?>
		<?php } else if (explode("/", $url)[0] == "about") { ?>
			<div class="d-flex flex-column flex-items-center flex-md-items-center">
				<div class="col-0 d-flex flex-items-center">
					<img src="https://misskey.04.si/files/a19a814c-2121-4fd6-88be-717b2d62e20f" alt="icon" style="width:250px;height: 250px; border-radius:250px;" />
				</div>
				<div class="col-12 col-md-10 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
					<h1 class="text-normal lh-condensed">Porlam Nicla</h1>
					<p class="h4 color-fg-muted text-normal mb-2">Developer of 04.si</p>
					<a class="color-fg-muted text-small" href="https://04.si/">https://04.si/</a>
				</div>
			</div>
		<?php } else if (explode("/", $url)[0] == "tools") { ?>
			<?php
			$name = explode("/", $url)[1];
			if (array_key_exists($name, $tools) && file_exists(__DIR__ . "/pages/$name.php")) {
				include_once(__DIR__ . "/pages/$name.php");
			?>

			<?php
			} else { ?>
				<?php http_response_code(404) ?>
				<div class="d-flex flex-column flex-row flex-items-center">
					<div class="col-12 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
						<h1 class="text-normal lh-condensed">404 Not found...</h1>
						<p class="h4 color-fg-muted text-normal mb-2">It page or tools is not found.</p>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<?php http_response_code(404) ?>
			<div class="d-flex flex-column flex-row flex-items-center">
				<div class="col-12 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
					<h1 class="text-normal lh-condensed">404 Not found...</h1>

					<p class="h4 color-fg-muted text-normal mb-2">It page or tools is not found.</p>
				</div>
			</div>
		<?php } ?>
	</main>

	<footer>
		<div class="d-flex flex-column flex-row flex-items-center">
			<div class="col-12 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
				<div>© 2022 <a href="https://twitter.com/nulland_dev" class="secondary-text">KernelUsami</a></div>
			</div>
		</div>
	</footer>

</body>

</html>