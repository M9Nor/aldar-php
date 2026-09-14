<!DOCTYPE html>
<html>
<head>
    <title>Aldar Real Estate</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
	<link rel="shortcut icon" type="image/x-icon" href="{{\Module::asset('frontend:assets/fav.svg')}}">
	<style>
		.desktop-image{
			display:block !important;
			margin: 0 auto;
			width: 85%;
		}
		.mobile-image{
			display:none !important;
			margin: 0 auto;
		}
		@media(max-width:768px){
			.desktop-image{
				display:none !important;
			}
			.mobile-image{
				display:block !important;
			}
		}
	</style>
</head>
<body>
	<div class="container-fluid text-center">
		<img src="{{\Module::asset('frontend:assets/Underconstruction.png')}}" class="img-fluid desktop-image" alt="">
		<img src="{{\Module::asset('frontend:assets/Underconstruction-mobile.png')}}" class="img-fluid mobile-image" alt="">
	</div>
</body>
</html>
