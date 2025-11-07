select * from cart natural join cart_item where c_id = 2;
drop table if exists temp_cart;
Set @o_id = 1;
CALL checkout(2, @o_id, @error_product);
