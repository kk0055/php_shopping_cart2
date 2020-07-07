<?php
session_start();
$product_ids = array();


if(filter_input(INPUT_POST,'add_to_cart')) {
  if(isset($_SESSION['shopping_cart'])) {

  $count = count($_SESSION['shopping_cart']);

  //idのキーにマッチした連続したarrayを作る
  $product_ids = array_column($_SESSION['shopping_cart'],'id');
  if(!in_array(filter_input(INPUT_GET,'id'),$product_ids)) {
    $_SESSION['shopping_cart'][$count] = array
    (
      'id' => filter_input(INPUT_GET,'id'),
      'name' => filter_input(INPUT_POST,'name'),
      'price' => filter_input(INPUT_POST,'price'),
      'quantity' => filter_input(INPUT_POST,'quantity')
    );
  }
  else{
    //productがすでに存在している。quantityを増やす
    //array keyを追加されたproductのidにマッチさせる
    for($i = 0 ;$i < count($product_ids); $i++) {
      if($product_ids[$i] == filter_input(INPUT_GET,'id')) {
        //item quantityをarrayに存在するproductに足す
         $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST,'quantity');
      }
    }
  }

}else{ 
  //if shopping cart does not exist,create first product with array key 0
  //create array using submitted form data,start from key 0 and fill it with values
  $_SESSION['shopping_cart'][0] = array
  (
    'id' => filter_input(INPUT_GET,'id'),
    'name' => filter_input(INPUT_POST,'name'),
    'price' => filter_input(INPUT_POST,'price'),
    'quantity' => filter_input(INPUT_POST,'quantity')
  );
}

}

if(filter_input(INPUT_GET,'action') == 'delete') {
  foreach($_SESSION['shopping_cart'] as $key => $item){
    if($item['id'] == filter_input(INPUT_GET,'id')){

      unset($_SESSION['shopping_cart'][$key]);
    }
  }
  $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}
// pre_r($_SESSION);

// function pre_r($array){
//   echo '<pre>';
//   print_r($array);
//   echo '</pre>';
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping cart</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="cart.css">
</head>
<body>
<div class="container mt-5">
<div class="row">
<?php

$connect = mysqli_connect('localhost','root','','cart');
$query = 'SELECT * FROM tblproduct ORDER BY id ASC';
$result = mysqli_query($connect,$query);

if($result) {
  if(mysqli_num_rows($result) > 0) {
   while($product = mysqli_fetch_assoc($result)) {
     ?>
    <div class="col-sm-5 col-md-3">
    <form method="post" action="cart.php?action=add&id=<?php echo $product['id']; ?>" >
     <div class="products">
     <img src="<?php echo $product['image']; ?>" class="img-responsive"  alt="">
     <h4 class="text-info"><?php echo $product['name'];?> </h4>
     <h4 >$<?php echo $product['price'];?> </h4>
     <input type="text" name="quantity" class="form-control" value="1">
     <input type="hidden" name="name" value="<?php echo $product['name'];?>">
     <input type="hidden" name="price" value="<?php echo $product['price'];?>">
     <input type="submit" name="add_to_cart" class="btn btn-info" style="margin-top:5px;" value="Add to Cart">
     </div>
    </form>
    </div>

     <?php
   }
  }
}

?>
</div>

<table class="tbl-cart" cellpadding="10" cellspacing="1">
<tbody>
<tr>
<th style="text-align:left;">Name</th>
<th style="text-align:right;" width="10%">Unit Price</th>
<th style="text-align:right;" width="5%">Quantity</th>
<th style="text-align:right;" width="10%">Price</th>
<th style="text-align:center;" width="5%">Remove</th>
</tr>	
<?php		
if(!empty($_SESSION['shopping_cart']));
$total = 0;
    foreach ( $_SESSION['shopping_cart'] as $item){
        $item_price = $item["quantity"]*$item["price"];
		?>
				<tr>
				<td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
        <td  style="text-align:right;"><?php echo "$".$item["price"]; ?></td>
				<td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
				<td  style="text-align:right;"><?php echo "$". number_format($item_price,2); ?></td>
				<td style="text-align:center;"><a href="cart.php?action=delete&id=<?php echo $item["id"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
				</tr>
				<?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
		
		}
		?>
 <tr>
<td colspan="1" align="right">Total:</td>
<td colspan="2" align="right"><?php echo $total_quantity; ?></td>
<td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
<td></td>
</tr>
<tr>
<td colspan="5">
<?php
if(isset($_SESSION['shopping_cart']));
if(count($_SESSION['shopping_cart']) > 0);
?>
<a href="#" >
<button type="button" class="btn btn-primary btn-lg btn-block">Checkout</button>
</a>

</td>
</tr>
</tbody>
</table>		

</div>
</body>
</html>

