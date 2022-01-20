<?php
	include("../server/connection.php");
	include '../set.php';
?>
<!DOCTYPE html>
<html>
<head>
	<?php include('../templates/head1.php');?>

	<script  type="text/javascript">

	function downloadToCSV(csv, filename) {
		var csvFile;
		var downloadLink;

		// CSV file
		csvFile = new Blob([csv], {type: "text/csv"});

		// Download link
		downloadLink = document.createElement("a");

		// File name
		downloadLink.download = filename;

		// Create a link to the file
		downloadLink.href = window.URL.createObjectURL(csvFile);

		// Hide download link
		downloadLink.style.display = "none";

		// Add the link to DOM
		document.body.appendChild(downloadLink);

		// Click download link
		downloadLink.click();
	}

	function exportTbToCSVformat(filename) {
		var csv = [];
		var rows = document.querySelectorAll("table tr");

		for (var i = 0; i < rows.length; i++) {
			var row = [], cols = rows[i].querySelectorAll("td, th");

			for (var j = 0; j < cols.length; j++)
				row.push(cols[j].innerText);

			csv.push(row.join(","));
		}

		// Download CSV file
		downloadToCSV(csv.join("\n"), filename);
	}
</script>


</head>
<body>
	<div class="contain h-100">
		<?php
			include('../sales/base.php');
		?>
		<div class="pr-1">
			<div>
				<h1 class="ml-4 pt-2" align="left"><i class="fas fa-shopping-cart"></i> Sprzedaż dzienna</h1>
			</div>
			<div class="table-responsive pl-5 pr-5">
			<table class="table table-bordered table-striped" id="sales_table" style="margin-top: 22px;">
				<thead>
					<tr>
						<th scope="col" class="column-text">Nr. zam</th>
						<th scope="col" class="column-text">Produkt</th>
						<th scope="col" class="column-text">Ilosc</th>
						<th scope="col" class="column-text">Cena</th>
						<th scope="col" class="column-text">Należność</th>
						<th scope="col" class="column-text">Rabat</th>



					</tr>
				</thead>
				<?php

				$sql_pocz = '2018-01-01 06:27:51';
				$sql_koni = '2022-01-17 23:59';
				$pdo = new PDO('mysql:dbname=pos', 'user', 'password');
				$sql = 'SELECT * FROM sales NATURAL JOIN sales_product';
				//$sql = 'SELECT `reciept_no`, `customer_id`, `username`, `discount`, `total`, `date` FROM sales WHERE date BETWEEN ? AND ?';
				$query = $pdo->prepare($sql);
				$query->bindValue(1, $sql_pocz, PDO::PARAM_INT);
				$query->bindValue(2, $sql_koni, PDO::PARAM_INT);
				$query->execute();

				$sql2 = 'SELECT price,qty FROM sales_product WHERE date BETWEEN ? AND ?';
				$query2 = $pdo->prepare($sql2);
				$query2->bindValue(1, $sql_pocz, PDO::PARAM_INT);
				$query2->bindValue(2, $sql_koni, PDO::PARAM_INT);
				$query2->execute();

				$gotowka = $pdo->query("SELECT SUM(total) FROM sales")->fetchColumn();
				$wynik = sprintf ("%.2f", $gotowka);

				?>
				<tbody>
					<?php foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) : ?>
					<tr>
					    <td><?php echo $row['reciept_no']; ?></td>
					    <td>
								<?php
								//echo $row['product_id'];
								$produkt = $row['product_id'];
								$produkt = $pdo->query("SELECT product_name FROM products WHERE product_no = '$produkt'")->fetchColumn();
								echo $produkt;
								?>
							</td>
					    <td>
								<?php
								echo $row['qty'];
								?>
							</td>
					    <td><?php echo $row['price']; ?> PLN</td>
					    <td><?php echo $row['total']; ?> PLN</td>
							<td><?php echo $row['discount']; ?> PLN</td>
					</tr>
					<?php endforeach;?>
				</tbody>
				<tfoot>
					<th colspan="3" class="text-right"></th>
					<th id="discount">Suma:</th>
					<th id="sales">
						<?php print $wynik; ?> PLN
					</th>
					<th></th>

				</tfoot>
			</table>
			</div>
		</div>
	</div>



</body>
</html>
