{header}
{tabla facturas}
{footer}

<script>
	//La funcion de escucha que agrega el frame, devuelve un array con el valor de la fila a que se dió clic
	function escucha_tabla_facturas(valores){
		$("#frame_pop").attr("href","<?=BASE_URL?>facturas/leer/"+valores[1]);
		$("#frame_pop").trigger("click");
	}
</script>
{escucha_tabla facturas}