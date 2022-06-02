select
 
	trim(u.login) as manejador,
	max(left(c1.name, 3)) as grupo,
	sum(il.price_subtotal) as valor,
	trim(substr(split_part(c1.name, '-', 2), 5, 50)) as descgrupo,
	sum(il.quantity) as cantidadvend,
	c2.name as categoria,
	p.default_code as item,
	p.name_template as nombre,
	o.name        as so_ordencomp,
	o.date_order  as so_fechaorden,
	
	po.name        as po_ordencomp,
	po.date_order as  po_fechaorden,
	avg(st.last_average_cost) as costo_promedio,
    max((st.cost + pi.price)/2) as costo,
    p.id as idproduct,
    
    --# QUERY I
    max(EXTRACT(YEAR FROM  in_date)) AS anio,
    max(EXTRACT(MONTH FROM  in_date)-1) as mes,
    max(EXTRACT(MONTH FROM  in_date)) as mesultmov,
    
    --# QUERY INV F
    sum(q.qty) as cantid
   
from
	sale_order o
	inner join sale_order_invoice_rel oi on 	o.id = oi.order_id
	inner join account_invoice cf on oi.invoice_id = cf.id and o.state = 'done'
	inner join account_invoice_line il on cf.id = il.invoice_id
	inner join product_product p on (ltrim(split_part(il.name, ']', 1), '['))= p.default_code
	inner join product_list_item i on i.product_id = p.id
	inner join product_template t on p.product_tmpl_id = t.id
	inner join product_category c1 on t.categ_id = c1.id
	inner join res_users u on t.product_manager = u.id
	inner join product_category c2 on c1.parent_id = c2.id
	
	inner join purchase_order_line ol on p.id = ol.product_id 
	inner join purchase_order po  on ol.order_id = po.id
	inner join stock_move st on po.name = st.origin
	inner join product_supplierinfo si ON p.product_tmpl_id=si.product_tmpl_id
	inner join pricelist_partnerinfo pi ON si.id=pi.suppinfo_id
	
	--# QUERY I
	inner join stock_quant q on q.product_id = p.id 
where 
 	q.location_id NOT IN('8','9') 
 		
    --# QUERY INVI
	/*
    and q.location_id NOT IN('8','9')
    */
		
group by
	
	left(c1.name, 3),
	trim(substr(split_part(c1.name, '-', 2), 5, 50)),
	u.login,
	o.state,
	left(cf.internal_number, 1) in('F', 'S', 'D', '0'),
	extract(year from cf.date_invoice),
	extract(month from 	cf.date_invoice),
	c2.name,
	c1.name,
	o.name,
	p.default_code,
	p.name_template,
	o.date_order,
	po.date_order,
	po.name,
	ol.state,
	po.date_order,
	cf.supplier_invoice,
    /*
	,EXTRACT(YEAR FROM  po.date_approve)
	,EXTRACT(MONTH FROM po.date_approve)
    */

    --# QUERY P
	p.id,  
	q.location_id,

	--# QUERY INVI
	q.product_id,
	q.in_date,
	q.location_id

having

	o.state = 'done'
	and c2.name is not null
	and c2.name not like '%CONTABILIDAD%'
	and c2.name not like '%SERVICIOS%'
	and c2.name not like '%ACTIVOS FIJOS%'
	and u.login in('SUAREZM', 'RODRIGUEZF', 'BARONF', 'PINILLOSM')
	
	--# QUERY V
	and extract(year from cf.date_invoice) = 2022
	and extract(month from cf.date_invoice) between 1 and 12
	and left(cf.internal_number, 1) in('F', 'S', 'D', '0')
	
	--# QUERY C            
	and trim(u.login) is not null
	/*
	and EXTRACT(YEAR FROM  po.date_approve)  = 2022 
	and EXTRACT(MONTH FROM  po.date_approve) between 1 and 12
	and cf.supplier_invoice is not null 
	and ol.state='done'
    */
	
	--# QUERY P
	and p.active = true
	and left(c1.name, 3) not in('Eva', 'Tod')
    
order by 
    split_part(c1.name, '-', 1) asc,
    left(c1.name,3) asc	
    
    
    
    
    