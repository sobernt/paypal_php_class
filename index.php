<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8" />
  <title>pay test</title>
</head>
 <body>
     <form method="post" action="pay.php">
         <label for="user_id">user_id</label><input name="user_id" id="user_id" value="<?php echo(rand ( 1 , 999 ));?>" ><br>
         <label for="order_id">order_id</label><input name="order_id" id="order_id" value="<?php echo(rand ( 1 , 9999 ));?>" ><br>
         <label for="summ">summ</label><input name="summ" id="summ" value="<?php echo(rand ( 1 , 9999 ).".".rand ( 1 ,99 ));?>" ><br>
         <input type="submit">
     </form>
 </body>
</html>

