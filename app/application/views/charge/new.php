<h1>Subscriptions</h1>
<?php foreach($products as $prod):?>
	<h3><?php echo $prod->name;?> <span><?php echo $prod->price_in_cents;?></span></h3>
	<p><?php echo $prod->description;?></p>
<?php endforeach;?>

<form action="/charge/subscribe" method="post">
	<label for="product">Plan:</label>
	<select name="product">
	<?php foreach($products as $prod):?>
		<option value="<?php echo $prod->id;?>"><?php echo $prod->name;?></option>
	<?php endforeach;?>
	</select>
	<fieldset>
		<legend>Customer Information</legend>
		<input type="text" name="firstName" placeholder="First Name"/>
		<input type="text" name="lastName" placeholder="Last Name"/>
		<input type="text" name="company" placeholder="Company"/>
		<input type="text" name="address" placeholder="Address"/>
		<input type="text" name="address2" placeholder="Address 2"/>
		<input type="text" name="city" placeholder="City"/>
		<input type="text" name="state" placeholder="State"/>
		<input type="text" name="zip" placeholder="Zip Code"/>
		<input type="text" name="country" placeholder="Country"/>
		<input type="text" name="email" placeholder="Email Address"/>
	</fieldset>
	<fieldset>
		<legend>Payment</legend>
		<!--<input type="text" name="b_firstName" placeholder="First Name"/>
		<input type="text" name="b_lastName" placeholder="Last Name"/>
		<input type="text" name="b_address" placeholder="Address"/>
		<input type="text" name="b_address2" placeholder="Address 2"/>
		<input type="text" name="b_city" placeholder="City"/>
		<input type="text" name="b_state" placeholder="State"/>
		<input type="text" name="b_zip" placeholder="Zip Code"/>
		<input type="text" name="b_country" placeholder="Country"/>-->
		<input type="text" name="ccNumber" placeholder="Credit Card Number"/>
		<input type="text" name="expMonth" placeholder="Expiration Month"/>
		<input type="text" name="expYear" placeholder="Expiration Year"/>
		<input type="text" name="cvv" placeholder="CVV"/>
	</fieldset>
	<input type="submit" name="submit" value="Subscribe!" />
</form>