<style>
	html,body{
		padding:	0;
		margin:0;
	}
</style>
<div style="font-family:Arial,Helvetica,sans-serif;	line-height: 1.5; font-weight: normal; font-size: 15px;	color: #2F3044;	min-height:	100%; margin:0;	padding:0; width:100%; background-color:#edf2f7">
	<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 auto; padding:0;	max-width:600px">
		<tbody>
			<tr>
				<td	align="center" valign="center" style="text-align:center; padding: 40px">
					<a href="{{env('APP_URL')}}" rel="noopener"	target="_blank">
						<img alt="Logo"	src="{{env('APP_LOGO_URL')}}"	style="height: 50px;"/>
					</a>
				</td>
			</tr>
			<tr>
				<td	align="left" valign="center">
					<div style="text-align:left; margin: 0 20px; padding: 40px;	background-color:#ffffff; border-radius: 6px">
						<!--begin:Email	content-->
						<div style="padding-bottom:	30px; font-size: 17px;">
							<strong>İki Faktörlü Kimlik Doğrulaması: {{$email}}!</strong>
						</div>
						<div style="padding-bottom:	30px">
							Aşağıdaki kodu ilgili alana yazınız.
						</div>
						<div style="padding-bottom:	40px; text-align:center;">
							{{$randomCode}}
						</div>
						<div style="border-bottom: 1px solid #eeeeee; margin: 15px 0"></div>
						<!--end:Email content-->
						<div style="padding-bottom:	10px">
								Saygılarımızla,<br>
								The	{{env('APP_NAME')}}	Team.
							<tr>
								<td	align="center" valign="center" style="font-size: 13px; text-align:center;padding: 20px;	color: #6d6e7c;">
									<p>Copyright ©
									<a href="{{env('APP_URL')}}" rel="noopener"	target="_blank">{{env('APP_NAME')}}</a>.</p>
								</td>
							</tr>
							</br>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
