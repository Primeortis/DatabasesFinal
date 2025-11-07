select * from cart natural join cart_item where c_id = 2;
select * from orders natural join order_item where o_id = 11;
CALL checkout(2, @o_id, @error_product);
select * from cart natural join cart_item where c_id = 2;
select * from orders natural join order_item where o_id = @o_id;
select @error_product