<?php
session_start();
 
class cart
{
 
	//aquí guardamos el contenido del cart
	private $cart = array();
 
	//seteamos el cart exista o no exista en el constructor
	public function __construct()
	{
		
		if(!isset($_SESSION["cart"]))
		{
			$_SESSION["cart"] = null;
			$this->cart["precio_total"] = 0;
			$this->cart["articles_total"] = 0;
		}
		$this->cart = $_SESSION['cart'];
	}
 
	//añadimos un producto al cart
	public function add($article = array())
	{
		//primero comprobamos el article a añadir, si está vacío o no es un 
		//array lanzamos una excepción y cortamos la ejecución
		if(!is_array($article) || empty($article))
		{
			throw new Exception("Error, el article no es un array!", 1);	
		}
 
		//nuestro carro necesita siempre un id producto, cantidad y precio article
		if(!$article["id"] || !$article["cantidad"] || !$article["precio"])
		{
			throw new Exception("Error, el article debe tener un id, cantidad y precio!", 1);	
		}
 
		//nuestro carro necesita siempre un id producto, cantidad y precio article
		if(!is_numeric($article["id"]) || !is_numeric($article["cantidad"]) || !is_numeric($article["precio"]))
		{
			throw new Exception("Error, el id, cantidad y precio deben ser números!", 1);	
		}
 
		//debemos crear un identificador único para cada producto
		$unique_id = md5($article["id"]);
 
		//creamos la id única para el producto
		$article["unique_id"] = $unique_id;
		
		//si no está vacío el cart lo recorremos 
		if(!empty($this->cart))
		{
			foreach ($this->cart as $row) 
			{
				//comprobamos si este producto ya estaba en el 
				//cart para actualizar el producto o insertar
				//un nuevo producto	
				if($row["unique_id"] === $unique_id)
				{
					//si ya estaba sumamos la cantidad
					$article["cantidad"] = $row["cantidad"] + $article["cantidad"];
				}
			}
		}
 
		//evitamos que nos pongan números negativos y que sólo sean números para cantidad y precio
		$article["cantidad"] = trim(preg_replace('/([^0-9\.])/i', '', $article["cantidad"]));
	    $article["precio"] = trim(preg_replace('/([^0-9\.])/i', '', $article["precio"]));
 
	    //añadimos un elemento total al array cart para 
	    //saber el precio total de la suma de este artículo
	    $article["total"] = $article["cantidad"] * $article["precio"];
 
	    //primero debemos eliminar el producto si es que estaba en el cart
	    $this->unset_producto($unique_id);
 
	    ///ahora añadimos el producto al cart
	    $_SESSION["cart"][$unique_id] = $article;
 
	    //actualizamos el cart
	    $this->update_cart();
 
	    //actualizamos el precio total y el número de artículos del cart
	    //una vez hemos añadido el producto
	    $this->update_precio_cantidad();
 
	}
	