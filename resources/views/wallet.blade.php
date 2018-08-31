@extends('layouts.app')
@section('content')
@if ($walletinfo != null)
<!-- Start page content -->
<section id="page-content" class="page-wrapper">
		<div class="about-sheltek-area ptb-115" style="padding-bottom: 30px !important;">
				<div class="container">
						<div></div>
						<div class="row">
								<table class="table table-bordered text-center">
										<thead>
										<tr>
												<th class="text-center">Cato Coin Balance</th>
												<th class="text-center">Current BTC Price</th>
												<th class="text-center">Last Sale Price ($)</th>
												<th class="text-center">CatoCoin Value ($)</th>
										</tr>
										</thead>
										<tbody>
										<tr>
												<td class="catocoin_balance">0</td>
												<td class="btc_price">0</td>
												<td class="last_sale_usd_price_of_coin">0</td>
												<td class="value_usd_of_catocoin">0</td>
										</tr>
										</tbody>
								</table>
						</div>
				</div>
		</div>
</section>

<section id="page-content" class="page-wrapper">
		<div class="about-sheltek-area ptb-115" style="padding-top: 0px !important;">
				<div class="container">
						<div class="row">
								<div class="elements-tab-1">
										<!-- <h5 class="mb-50">Accounts</h5> -->
										<!-- Nav tabs -->
										<ul class="nav nav-tabs">
												<li class="active"><a href="#transections"  data-toggle="tab">Transactions</a></li>
												<li><a href="#aBalance_1"  data-toggle="tab">Address Balances</a></li>
												<li><a href="#MNStatus_1"  data-toggle="tab">Master Node Status</a></li>
										</ul>
										<!-- Tab panes -->
										<div class="tab-content">
												<div class="tab-pane fade in active" id="transections">
														<table class="table table-bordered text-center">
																<thead>
																<tr>
																		<th class="text-center">#</th>
																		<th class="text-center">Date</th>
																		<th class="text-center">Type</th>
																		<th class="text-center">Account</th>
																		<th class="text-center">Amount</th>
																</tr>
																</thead>
																<tbody>
                                @for ($i=49; $i >= 0; $i--)
																@if (isset($transactions[$i]))
																<tr>
																		<th scope="row">{{$transactions[$i]['txid']}}</th>
																		<td>{{date('Y-m-d H:i:s', $transactions[$i]['time'])}}</td>
																		<td>{{$transactions[$i]['type']}}</td>
																		<td>{{$transactions[$i]['account']}}</td>
																		<td>{{$transactions[$i]['amount']}}</td>
																</tr>
																@endif
                                @endfor
																</tbody>
														</table>
												</div>
												<div class="tab-pane fade" id="aBalance_1">
														<table class="table table-bordered text-center">
																<thead>
																<tr>
																		<th class="text-center">#</th>
																		<th class="text-center">Address</th>
																		<th class="text-center">Balance</th>
																</tr>
																</thead>
																<tbody>
                                <?php $count=0; ?>
                                @foreach ($addresses as $item)
																@if ($item["balance"] > 0)
                                <?php $count++; ?>
																<tr>
																		<th scope="row">{{$count}}</th>
																		<td>@if (is_array($item)) {{$item["item_addr"]}} @endif</td>
																		<td>@if (is_array($item)) {{$item["balance"]}} @endif</td>
																</tr>
																@endif
                                @endforeach
																</tbody>
														</table>
												</div>
												<div class="tab-pane fade" id="MNStatus_1">
														<table class="table table-bordered text-center">
																<thead>
																<tr>
																		<th class="text-center">Alias</th>
																		<th class="text-center">Address</th>
																		<th class="text-center">Protocol</th>
																		<th class="text-center">Collateral</th>
																		<th class="text-center">Status</th>
																		<th class="text-center">Active</th>
																		<th class="text-center">Last Seen</th>
																		<th class="text-center">Pubkey</th>
																</tr>
																</thead>
																<tbody>
																	@foreach ($masternodes as $masternode)
																		<tr>
																			<td>{{$masternode['alias']}}</td>
																			<td>{{$masternode['address']}}</td>
																			<td>{{$masternode['version']}}</td>
																			<td>3150</td>
																			<td>{{$masternode['status']}}</td>
																			<td>{{$masternode['activetime']}}</td>
																			<td>{{$masternode['lastseen']}}</td>
																			<td>{{$masternode['public_key']}}</td>
																		</tr>
																	@endforeach
																</tbody>
														</table>
												</div>
										</div>
								</div>
						</div>
				</div>
		</div>
</section>
@else
<section id="page-content" class="page-wrapper" style="padding:300px 0px;text-align:center; font-size:30px">
	Wallet offline - support has been notified!
</section>
@endif
<script>
function CommaFormatted(amount)
{
	var delimiter = ","; // replace comma if desired
	console.log(amount);
	amount = amount + '';
	var a = amount.split('.',2)
	var d='';
	if (a[1])
		d = a[1];
	var i = parseInt(a[0]);
	if(isNaN(i)) { return ''; }
	var minus = '';
	if(i < 0) { minus = '-'; }
	i = Math.abs(i);
	var n = new String(i);
	var a = [];
	while(n.length > 3)
	{
		var nn = n.substr(n.length-3);
		a.unshift(nn);
		n = n.substr(0,n.length-3);
	}
	if(n.length > 0) { a.unshift(n); }
	n = a.join(delimiter);
	if(d.length < 1) { amount = n; }
	else { amount = n + '.' + d; }
	amount = minus + amount;
	return amount;
}

$(document).ready(function(){
	jQuery.get('https://api.coinmarketcap.com/v1/ticker/bitcoin/', function(data, status){
		btc_price = data[0].price_usd;
		$('.btc_price').html('$'+CommaFormatted(parseFloat(data[0].price_usd).toFixed(2)));

		jQuery.get('https://api.crypto-bridge.org/api/v1/ticker', function(data, status){
			var price = 0;
			for (var i in data){
				if (data[i]['id'] == "CATO_BTC"){
						price = data[i]['last'];
				}
			}
			//btc_value_of_coins = price * cato_balance;
			price = price * btc_price;
			cato_balance = parseFloat("{{$balance}}");
			total_price_balance = cato_balance * price;
			$('.value_usd_of_catocoin').html('$'+CommaFormatted(total_price_balance.toFixed(5)));
			$('.last_sale_usd_price_of_coin').html('$'+CommaFormatted(price.toFixed(5)));
		});
	});

	@if (isset($balance))
		var cato_balance = parseFloat("{{$balance}}");
		$('.catocoin_balance').html(CommaFormatted(cato_balance));

		// jQuery.get('https://www.worldcoinindex.com/apiservice/ticker?key=HmnCf2MbMnG5vGvrLZ3B9hJhgcTc4Y&label=catobtc&fiat=btc', function(data, status){
		// 	price = data['Markets'][0]['Price'];
		// 	btc_value_of_coins = price * cato_balance;
		// 	$('.btc_value_of_coins').html(CommaFormatted(btc_value_of_coins.toFixed(5)));
		// });

		jQuery.get('https://www.worldcoinindex.com/apiservice/ticker?key=HmnCf2MbMnG5vGvrLZ3B9hJhgcTc4Y&label=catobtc&fiat=usd', function(data, status){
			price = data['Markets'][0]['Price'];
			//value_of_catocoins = price * cato_balance;

		});
	@endif
});
</script>
<!-- End page content -->
@endsection
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
