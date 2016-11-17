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
	/método que actualiza el precio total y la cantidad
	//de productos total del carrito
	private function update_precio_cantidad()
	{
		//seteamos las variables precio y artículos a 0
		$precio = 0;
		$articulos = 0;
 
		//recorrecmos el contenido del carrito para actualizar
		//el precio total y el número de artículos
		foreach ($this->carrito as $row) 
		{
			$precio += ($row['precio'] * $row['cantidad']);
			$articulos += $row['cantidad'];
		}
 
		//asignamos a articulos_total el número de artículos actual
		//y al precio el precio actual
		$_SESSION['carrito']["articulos_total"] = $articulos;
		$_SESSION['carrito']["precio_total"] = $precio;
 
		//refrescamos él contenido del carrito para que quedé actualizado
		$this->update_carrito();
	}
 
	//método que retorna el precio total del carrito
	public function precio_total()
	{
		//si no está definido el elemento precio_total o no existe el carrito
		//el precio total será 0
		if(!isset($this->carrito["precio_total"]) || $this->carrito === null)
		{
			return 0;
		}
		//si no es númerico lanzamos una excepción porque no es correcto
		if(!is_numeric($this->carrito["precio_total"]))
		{
			throw new Exception("El precio total del carrito debe ser un número", 1);	
		}
		//en otro caso devolvemos el precio total del carrito
		return $this->carrito["precio_total"] ? $this->carrito["precio_total"] : 0;
	}
 
	//método que retorna el número de artículos del carrito
	public function articulos_total()
	{
		//si no está definido el elemento articulos_total o no existe el carrito
		//el número de artículos será de 0
		if(!isset($this->carrito["articulos_total"]) || $this->carrito === null)
		{
			return 0;
		}
		//si no es númerico lanzamos una excepción porque no es correcto
		if(!is_numeric($this->carrito["articulos_total"]))
		{
			throw new Exception("El número de artículos del carrito debe ser un número", 1);	
		}
		//en otro caso devolvemos el número de artículos del carrito
		return $this->carrito["articulos_total"] ? $this->carrito["articulos_total"] : 0;
	}
 
	//este método retorna el contenido del carrito
	public function get_content()
	{
		//asignamos el carrito a una variable
		$carrito = $this->carrito;
		//debemos eliminar del carrito el número de artículos
		//y el precio total para poder mostrar bien los artículos
		//ya que estos datos los devuelven los métodos 
		//articulos_total y precio_total
		unset($carrito["articulos_total"]);
		unset($carrito["precio_total"]);
		return $carrito == null ? null : $carrito;
	}
 
	//método que llamamos al insertar un nuevo producto al 
	//carrito para eliminarlo si existia, así podemos insertarlo
	//de nuevo pero actualizado
	private function unset_producto($unique_id)
	{
		unset($_SESSION["carrito"][$unique_id]);
	}
 
	//para eliminar un producto debemos pasar la clave única
	//que contiene cada uno de ellos
	public function remove_producto($unique_id)
	{
		//si no existe el carrito
		if($this->carrito === null)
		{
			throw new Exception("El carrito no existe!", 1);
		}
 
		//si no existe la id única del producto en el carrito
		if(!isset($this->carrito[$unique_id]))
		{
			throw new Exception("La unique_id $unique_id no existe!", 1);
		}
 
		//en otro caso, eliminamos el producto, actualizamos el carrito y 
		//el precio y cantidad totales del carrito
		unset($_SESSION["carrito"][$unique_id]);
		$this->update_carrito();
		$this->update_precio_cantidad();
		return true;
	}
 
	//eliminamos el contenido del carrito por completo
	public function destroy()
	{
		unset($_SESSION["carrito"]);
		$this->carrito = null;
		return true;
	}
 
	//actualizamos el contenido del carrito
	public function update_carrito()
	{
		self::__construct();
	}
 
}
	