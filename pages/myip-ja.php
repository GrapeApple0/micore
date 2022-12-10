<div class="d-flex flex-column flex-row flex-items-center">
	<div class="col-8 d-flex flex-column flex-justify-center flex-items-center pl-md-4">
		<label>IPチェッカー</label>
		<p>自分のIPアドレスを表示します</p>
		<script>
			fetch('https://v4.mico.re/myip.php')
				.then(function(response) {
					return response.text();
				})
				.then(function(response) {
					ip = JSON.parse(response);
					document.getElementById("ipv4-addr").innerHTML = "IPv4:" + ip.ip;
				}).catch(function(err) {
					document.getElementById("ipv4-addr").innerHTML = "IPv4:None";
				});

			fetch('https://v6.mico.re/myip.php')
				.then(function(response) {
					return response.text();
				})
				.then(function(response) {
					ip = JSON.parse(response);
					document.getElementById("ipv6-addr").innerHTML = "IPv6:" + ip.ip;
				}).catch(function(err) {
					document.getElementById("ipv6-addr").innerHTML = "IPv6:None";
				});
		</script>
		<div class="col-sm-12 col-md-6 col-xl-4 float-left p-4">
			<p class="mb-0 color-fg-muted" id="ipv4-addr">IPv4</p>
		</div>
		<div class="col-sm-12 col-md-6 col-xl-4 float-left p-4">
			<p class="mb-0 color-fg-muted" id="ipv6-addr">IPv6</p>
		</div>
	</div>
</div>