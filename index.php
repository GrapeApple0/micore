<?php
session_start();
$url = ltrim(htmlspecialchars($_SERVER["REQUEST_URI"], ENT_QUOTES, 'UTF-8'), "/");
if (!file_exists(__DIR__ . '/config/settings.json')) {
	http_response_code(500);
	echo "Error:'settings.json' is not found.";
	exit;
}
$settings_json = file_get_contents(__DIR__ . '/config/settings.json');
$settings = json_decode($settings_json, true);

if (!file_exists(__DIR__ . '/config/theme.json')) {
	http_response_code(500);
	echo "Error:'theme.json' is not found.";
	exit;
}
$theme_json = file_get_contents(__DIR__ . '/config/theme.json');
$theme = json_decode($theme_json, true);

$lang = filter_input(INPUT_COOKIE, "lang");
if ($lang == "" || !file_exists(__DIR__ . "/config/tools-$lang.json")) {
	setcookie("lang", "ja", path: "/");
	header("location:" . "/");
	exit;
} else {
	setcookie("lang", $lang, path: "/");
}
$lang = filter_input(INPUT_COOKIE, "lang");
if (!file_exists(__DIR__ . "/config/tools-$lang.json")) {
	$lang = "ja";
}

$tools_json = file_get_contents(__DIR__ . "/config/tools-$lang.json");
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
	<link href="https://<?php echo $settings["domain"] ?>/style.css" rel="stylesheet" />
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
			color: <?php echo $theme["primary-text"] ?>;
			box-shadow: none;
		}

		.primary-button:hover {
			background-color: <?php echo $theme["primary-button-hover"] ?>;
			color: <?php echo $theme["primary-text"] ?>;
		}

		.link {
			color: <?php echo $theme["link"] ?>;
		}

		.box {
			background-color: <?php echo $theme["box"] ?>;
			border-style: solid;
			border-width: 1px;
			border-radius: 6px;
		}

		.box-row {
			padding: 16px;
			margin-top: -1px;
			list-style-type: none;
			border-top-style: solid;
			border-top-width: 1px;
		}

		.textbox::placeholder {
			color: <?php echo $theme["textbox"] ?>;
		}

		.textbox {
			color: <?php echo $theme["textbox"] ?>;
			background-color: <?php echo $theme["textbox-bg"] ?>;
			border: 1px solid <?php echo $theme["textbox-border"] ?>;
			border-radius: 6px;
			padding: 5px 12px;
			font-size: 14px;
			line-height: 20px;
			outline: none;
		}

		.textbox:focus:not(:focus-visible) {
			border-color: <?php echo $theme["textbox-border-focus"] ?>;
		}

		.textbox:focus-visible {
			border-color: <?php echo $theme["textbox-border-focus"] ?>;
			box-shadow: inset 0 0 0 0 <?php echo $theme["textbox-border-focus"] ?>;
		}

		.menu {
			background-color: <?php echo $theme["menu"] ?>;
			border: none;
		}

		.menu-item {
			background-color: <?php echo $theme["menu-item"] ?>;
			border: 0;
		}
	</style>
</head>

<body class="secondary-color">
	<div class="Header secondary-color" style="height: 50px;">
		<div class="Header-item">
			<a href="/" class="Header-link f4 d-flex flex-items-center">
				<span class="secondary-text"><?php echo $settings["name"] . "@$lang" ?></span>
			</a>
		</div>
		<div class="Header-item Header-item--full"></div>
		<div class="Header-item mr-1">
			<a class="link" href="/about">About</a>
		</div>
		<div class="Header-item mr-1">
			<a class="link" href="/donate">Donate</a>
		</div>
		<div class="Header-item mr-1">
			<div class="d-flex flex-justify-end position-relative">
				<details class="details-reset details-overlay">
					<summary class="link" aria-haspopup="false">
						Lang
					</summary>
					<div class="SelectMenu right-0">
						<div class="SelectMenu-modal" style="width: 75px;background-color: transparent;border-color: transparent;">
							<div class="SelectMenu-list menu">
								<?php foreach ($settings["lang"] as $key => $lang) { ?>
									<a class="SelectMenu-item menu-item primary-text" href="/lang/<?php echo $lang ?>"><?php echo $lang ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</details>
			</div>
		</div>
	</div>
	<main>
		<?php if ($url == "") { ?>
			<div class="container-lg clearfix">
				<?php foreach ($tools as $key => $value) { ?>
					<?php if ($value["hidden"] == false) { ?>
						<div class="fill-width col-md-12 col-lg-6 col-xl-4 float-left p-4">
							<div class="box">
								<div class="box-row">
									<h3 class="m-0 primary-text"><?php echo $value["title"] ?></h3>
								</div>
								<div class="box-row">
									<p class="mb-0 primary-text">
										<?php echo $value["description"] ?>
									</p>
								</div>
								<div class="box-row">
									<a class="btn btn-block primary-button" href="<?php echo $value["url"] ?>">
										<?php
										$lang = filter_input(INPUT_COOKIE, "lang");
										if (!file_exists(__DIR__ . '/config/useit.json')) {
											http_response_code(500);
											echo "Error:'useit.json' is not found.";
											exit;
										}
										$useit_json = file_get_contents(__DIR__ . '/config/useit.json');
										$useit = json_decode($useit_json, true);
										echo $useit["$lang"];
										?>
									</a>
								</div>
							</div>
						</div><?php } ?>
				<?php } ?>
			</div>
		<?php } else if (explode("/", $url)[0] == "donate") { ?>
			<div class="d-flex flex-column flex-items-center flex-md-items-center">
				<p class="primary-text">Coming soon...</p>
			</div>
		<?php } else if (explode("/", $url)[0] == "eula") { ?>
			<?php $lang = filter_input(INPUT_COOKIE, "lang");
			if (!file_exists(__DIR__ . "/pages/eula-$lang.php")) {
				http_response_code(500);
				echo "Error:'eula-$lang.php' is not found.";
				exit;
			}
			include_once(__DIR__ . "/pages/eula-$lang.php"); ?>
		<?php } else if (explode("/", $url)[0] == "privacy") { ?>
			<?php $lang = filter_input(INPUT_COOKIE, "lang");
			if (!file_exists(__DIR__ . "/pages/privacy-$lang.php")) {
				http_response_code(500);
				echo "Error:'privacy-$lang.php' is not found.";
				exit;
			}
			include_once(__DIR__ . "/pages/privacy-$lang.php"); ?>
		<?php } else if (explode("/", $url)[0] == "about") { ?>
			<?php $lang = filter_input(INPUT_COOKIE, "lang");
			if (!file_exists(__DIR__ . '/config/useit.json')) {
				http_response_code(500);
				echo "Error:'useit.json' is not found.";
				exit;
			}
			include_once(__DIR__ . "/pages/about-$lang.php"); ?>
		<?php } else if (explode("/", $url)[0] == "tools") { ?>
			<?php
			$name = explode("/", $url)[1];
			$lang = filter_input(INPUT_COOKIE, "lang");
			if ($lang == "" || !file_exists(__DIR__ . "/config/tools-$lang.json")) {
				setcookie("lang", "ja", path: "/");
			} else {
				setcookie("lang", $lang, path: "/");
			}
			if (file_exists(__DIR__ . "/pages/$name-$lang.php")) {
				include_once(__DIR__ . "/pages/$name-$lang.php");
			} else { ?>
				<?php http_response_code(404) ?>
				<div class="d-flex flex-column flex-row flex-items-center">
					<div class="col-12 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
						<h1 class="text-normal lh-condensed">404 Not found...</h1>
						<p class="h4 color-fg-muted text-normal mb-2">This page or tools is not found.</p>
					</div>
				</div>
			<?php } ?>
		<?php } else if (explode("/", $url)[0] == "lang") { ?>
			<?php
			$lang = explode("/", $url)[1];
			if (file_exists(__DIR__ . "/config/tools-$lang.json")) {
				setcookie("lang", $lang, path: "/");
			} else {
				setcookie("lang", "ja", path: "/");
			}
			header("location:" . "/");
			exit;
			?>
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
		<div class="col-12 d-flex flex-justify-center flex-items-center pl-md-4">
			<label class="primary-text">© 2022 <a href="https://twitter.com/nulland_dev" class="secondary-text">KernelUsami</a></label>
			<label class="primary-text" style="margin-left: 5px;"><a href="/eula" class="secondary-text">EULA</a></label>
			<label class="primary-text" style="margin-left: 5px;"><a href="/eula" class="secondary-text">Privacy Policy</a></label>
		</div>
	</footer>

</body>

</html>