{header}
{incluir menu.tpl}
<ul class="errores">
{errores}
</ul>
<form method="post" action="<?= BASE_URL ?>login/acceso/">
	<table border=0px>
		<tr><td><label>Usario</label></td><td><input type="text" name="correo" autocomplete="off" /></td></tr>
		<tr><td><label>Password</label></td><td><input type="password" name="password" /></td></tr>
		<tr><td><input type="submit" name="Entrar" value="Entrar" /></td></tr>
	</table>
</form>
{footer}