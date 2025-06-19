<?php require_once "../includes/header.php";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $nuevo_admin = $_POST["id"];
    $stmt1 = $conn->prepare("UPDATE usuarios SET rol='administrador' WHERE id=?");
    $stmt1->bind_param("i", $nuevo_admin);
    $stmt1->execute();
    $stmt2 = $conn->prepare("UPDATE usuarios SET rol='usuario' WHERE id=?");
    $stmt2->bind_param("i", $_SESSION["id"]);
    $stmt2->execute();
    $_SESSION["rol"] = "usuario";
    header("Location: ../index.php");
    exit;
}
$result = $conn->query("SELECT id, nombre FROM usuarios WHERE rol != 'administrador'");
?>
<h2>Transferencia de Rol de Administrador</h2>
<form method="POST">
    <label>Selecciona un nuevo administrador:</label>
    <select name="id" class="form-control mb-2">
        <?php while($row = $result->fetch_assoc()): ?>
            <option value="<?= $row["id"] ?>"><?= $row["nombre"] ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-warning">Transferir Rol</button>
</form>
<?php require_once "../includes/footer.php"; ?>
