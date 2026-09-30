<?php

header('Content-Type: text/html; charset=utf-8');

$command = 'python3 /home/pi/deyecloud/python/dades_estacio.py 2>&1';

$output = array();
$returnCode = 0;

exec($command, $output, $returnCode);

if ($returnCode !== 0) {
    echo "<p>Error executant Python:</p>";
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    exit;
}

$resultat = implode("\n", $output);

// Busquem els tres valors
preg_match("/'consumptionPower':\s*([-0-9.]+)/", $resultat, $consumption);
preg_match("/'wirePower':\s*([-0-9.]+)/", $resultat, $wire);
preg_match("/'generationPower':\s*([-0-9.]+)/", $resultat, $generation);

if (!isset($consumption[1]) || !isset($wire[1]) || !isset($generation[1])) {
    echo "<p>No s'han pogut trobar els valors.</p>";
    exit;
}

// Classe segons el valor
$consumptionClass = ($consumption[1] >= 0) ? 'positiu' : 'negatiu';
$wireClass = ($wire[1] < 0) ? 'positiu' : 'negatiu';
$generationClass = ($generation[1] >= 0) ? 'positiu' : 'negatiu';


// ------- Agafem historic mensual ---------------------------------------------------

$command = 'python3 /home/pi/deyecloud/python/mes.py 2>&1';

$output = array();
$returnCode = 0;

exec($command, $output, $returnCode);

if ($returnCode !== 0) {
    echo "<p>Error executant Python:</p>";
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    exit;
}

$resultat = implode("\n", $output);

preg_match("/'purchaseValue':\s*([-0-9.]+)/", $resultat, $purchaseMes);
preg_match("/'generationValue':\s*([-0-9.]+)/", $resultat, $generationMes);
preg_match("/'gridValue':\s*([-0-9.]+)/", $resultat, $gridMes);

$purchaseMesClass = ($purchaseMes[1] >= 0) ? 'positiu' : 'negatiu';
$generationMesClass = ($generationMes[1] >= 0) ? 'positiu' : 'negatiu';
$gridMesClass = ($gridMes[1] >= 0) ? 'positiu' : 'negatiu';


// ----- Agafem totals diaris --------------------------------------------------------

$command = 'python3 /home/pi/deyecloud/python/dia1.py 2>&1';

$output = array();
$returnCode = 0;

exec($command, $output, $returnCode);

if ($returnCode !== 0) {
    echo "<p>Error executant Python:</p>";
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    exit;
}

$resultat = implode("\n", $output);

/*preg_match("/'purchaseValue':\s*([-0-9.]+)/", $resultat, $purchaseDia);
preg_match("/'generationValue':\s*([-0-9.]+)/", $resultat, $generationDia);
preg_match("/'gridValue':\s*([-0-9.]+)/", $resultat, $gridDia);
*/
preg_match("/Produccio avui:\s*([-0-9.]+)\s*kWh/", $resultat, $generationDia);
preg_match("/Venut avui:\s*([-0-9.]+)\s*kWh/", $resultat, $gridDia);
preg_match("/Comprat avui:\s*([-0-9.]+)\s*kWh/", $resultat, $purchaseDia);


$purchaseDiaClass = ($purchaseDia[1] >= 0) ? 'positiu' : 'negatiu';
$generationDiaClass = ($generationDia[1] >= 0) ? 'positiu' : 'negatiu';
$gridDiaClass = ($gridDia[1] >= 0) ? 'positiu' : 'negatiu';

?>

<!DOCTYPE html>
<html lang="ca">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="refresh" content="120">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inversor Deye</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            background: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .contenidor {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .valor {
            position: relative;
            background: #1e1e1e;
            border-radius: 18px;
            margin: 18px 0;
            padding: 25px 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .literal {
            font-size: 26px;
            margin-bottom: 10px;
        }

        .literal i {
            font-size: 21px;
            margin-right: 8px;
            opacity: 0.85;
        }

        .numero {
            font-size: 52px;
            font-weight: bold;
        }

        .unitat {
            font-size: 24px;
            margin-left: 5px;
        }

        .positiu {
            color: #00e676;
        }

        .negatiu {
            color: #ff5252;
        }

        h1 {
            font-size: 42px;
            margin: 10px 0 30px 0;
            font-weight: bold;
        }

        .updated {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
            font-size: 14px;
            opacity: 0.6;
        }


        /* Botó euros */

        .boto-euro {
            position: absolute;
            right: 12px;
            bottom: 12px;
            width: 42px;
            height: 42px;
            padding: 0;
            border: none;
            border-radius: 10px;
            background: #333333;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        .boto-euro:hover {
            background: #444444;
        }
    </style>

</head>

<body>

    <div class="contenidor">

        <h1>Inversor Deye</h1>


        <div class="updated">
            Última actualització <?php echo date('d/m/Y H:i:s'); ?>
        </div>


        <!-- PRODUCCIÓ SOLAR ACTUAL -->

        <div class="valor">

            <div class="literal">
                <i class="fa-solid fa-sun"></i>
                Producció solar actual
            </div>

            <span class="numero <?php echo $generationClass; ?>">
                <?php echo $generation[1]; ?>
            </span>

            <span class="unitat">W</span>

        </div>


        <!-- CONSUM ACTUAL -->

        <div class="valor">

            <div class="literal">
                <i class="fa-solid fa-house"></i>
                Consum actual
            </div>

            <span class="numero <?php echo $consumptionClass; ?>">
                <?php echo $consumption[1]; ?>
            </span>

            <span class="unitat">W</span>

        </div>


        <!-- COMPRAT ACTUAL -->

        <div class="valor">

            <div class="literal">
                <i class="fa-solid fa-bolt"></i>
                Comprat actual
            </div>

            <span class="numero <?php echo $wireClass; ?>">
                <?php echo $wire[1]; ?>
            </span>

            <span class="unitat">W</span>

        </div>


        <!-- TOTAL COMPRAT MENSUAL -->

        <div class="valor">

            <div id="purchaseMesTitle" class="literal">
                <i class="fa-solid fa-cart-shopping"></i>
                Total comprat mensual
            </div>

            <span id="purchaseMesValor" class="numero <?php echo $purchaseMesClass; ?>">
                <?php echo $purchaseMes[1]; ?>
            </span>

            <span id="purchaseMesUnitat" class="unitat">kWh</span>

            <button type="button" id="purchaseMesEuro" class="boto-euro" title="Convertir a euros">

                <i class="fa-solid fa-euro-sign"></i>

            </button>

        </div>

        <!-- TOTAL COMPRAT DIARI -->

        <div class="valor">

            <div class="literal">
                <i class="fa-solid fa-bag-shopping"></i>
                Total comprat diari
            </div>

            <span id="purchaseDiaValor" class="numero <?php echo $purchaseDiaClass; ?>">
                <?php echo $purchaseDia[1]; ?>
            </span>

            <span id="purchaseDiaUnitat" class="unitat">kWh</span>

            <button type="button" id="purchaseDiaEuro" class="boto-euro" title="Convertir a euros">

                <i class="fa-solid fa-euro-sign"></i>

            </button>

        </div>


        <!-- TOTAL VENUT / PRODUCCIÓ DIÀRIA -->

        <div class="valor">

            <div id="gridDiaTitle" class="literal">
                <i class="fa-solid fa-coins"></i>Total venut / producció diària
            </div>

            <span id="gridDiaValor" class="numero <?php echo $generationDiaClass; ?>">
                <?php echo $gridDia[1]; ?> / <?php echo $generationDia[1]; ?>
            </span>

            <span id="gridDiaUnitat" class="unitat">kWh</span>

            <button type="button" id="gridDiaEuro" class="boto-euro" title="Convertir a euros">

                <i class="fa-solid fa-euro-sign"></i>

            </button>

        </div>


        <!-- TOTAL VENUT / PRODUCCIÓ MENSUAL -->

        <div class="valor">

            <div id="gridMesTitle" class="literal">
                <i class="fa-solid fa-money-bill-wave"></i>Total venut / producció mensual
            </div>

            <span id="gridMesValor" class="numero <?php echo $generationMesClass; ?>">
                <?php echo $gridMes[1]; ?> / <?php echo $generationMes[1]; ?>
            </span>

            <span id="gridMesUnitat" class="unitat">kWh</span>

            <button type="button" id="gridMesEuro" class="boto-euro" title="Convertir a euros">

                <i class="fa-solid fa-euro-sign"></i>

            </button>

        </div>


    </div>


    <!-- CONVERSIÓ kWh -> € -->

    <script>

        document.addEventListener('DOMContentLoaded', function () {


            // ============================================================
            // TOTAL COMPRAT MENSUAL
            // ============================================================

            var boto_purchaseMesEuro = document.getElementById('purchaseMesEuro');

            if (boto_purchaseMesEuro) {
				
				var purchaseMesTitle = document.getElementById('purchaseMesTitle');

                var valorElement_purchaseMesValor =
                    document.getElementById('purchaseMesValor');

                var unitatElement_purchaseMesUnitat =
                    document.getElementById('purchaseMesUnitat');

                var enEuros_purchaseMesUnitat = false;

                var valorKwh_purchaseMesUnitat = parseFloat(valorElement_purchaseMesValor.textContent.trim());
				
				//----------------------------------------------------------------------
				var valorElement_gridMesValor = document.getElementById('gridMesValor');

                var valorKwh_gridMesUnitat = parseFloat(
                    valorElement_gridMesValor.textContent.trim()
                );
					
                var eurosVenutMes = valorKwh_gridMesUnitat * 0.06;
				
				//---------------------------------------------------------------------

                if (!isNaN(valorKwh_purchaseMesUnitat)) {

                    boto_purchaseMesEuro.addEventListener('click', function () {

                        if (!enEuros_purchaseMesUnitat) {
							
							purchaseMesTitle.innerHTML = "<i class=\"fa-solid fa-cart-shopping\"></i>Total comprat mes / Total net mes";

                            // kWh -> euros
                            var eurosCompratMes = valorKwh_purchaseMesUnitat * 0.16;
							
							var eurosNetMes = eurosCompratMes - eurosVenutMes

                            valorElement_purchaseMesValor.textContent = eurosCompratMes.toFixed(2) + " / " + eurosNetMes.toFixed(2);

                            unitatElement_purchaseMesUnitat.textContent = '€';

                            boto_purchaseMesEuro.title = 'Mostrar kWh';

                            enEuros_purchaseMesUnitat = true;

                        } else {
							
							purchaseMesTitle.innerHTML = "<i class=\"fa-solid fa-cart-shopping\"></i>Total comprat mensual";

                            // euros -> kWh
                            valorElement_purchaseMesValor.textContent =
                                valorKwh_purchaseMesUnitat;

                            unitatElement_purchaseMesUnitat.textContent = 'kWh';

                            boto_purchaseMesEuro.title = 'Convertir a euros';

                            enEuros_purchaseMesUnitat = false;

                        }

                    });

                }

            }


            // ============================================================
            // TOTAL COMPRAT DIARI
            // ============================================================

            var boto_purchaseDiaEuro = document.getElementById('purchaseDiaEuro');

            if (boto_purchaseDiaEuro) {

                var valorElement_purchaseDiaValor =
                    document.getElementById('purchaseDiaValor');

                var unitatElement_purchaseDiaUnitat =
                    document.getElementById('purchaseDiaUnitat');

                var enEuros_purchaseDiaUnitat = false;

                var valorKwh_purchaseDiaUnitat = parseFloat(
                    valorElement_purchaseDiaValor.textContent.trim()
                );

                if (!isNaN(valorKwh_purchaseDiaUnitat)) {

                    boto_purchaseDiaEuro.addEventListener('click', function () {

                        if (!enEuros_purchaseDiaUnitat) {

                            // kWh -> euros
                            var euros = valorKwh_purchaseDiaUnitat * 0.16;

                            valorElement_purchaseDiaValor.textContent =
                                euros.toFixed(2);
                            unitatElement_purchaseDiaUnitat.textContent = '€';
                            boto_purchaseDiaEuro.title = 'Mostrar kWh';
                            enEuros_purchaseDiaUnitat = true;

                        } else {

                            // euros -> kWh
                            valorElement_purchaseDiaValor.textContent =
                                valorKwh_purchaseDiaUnitat;
                            unitatElement_purchaseDiaUnitat.textContent = 'kWh';
                            boto_purchaseDiaEuro.title = 'Convertir a euros';
                            enEuros_purchaseDiaUnitat = false;

                        }

                    });
                }
            }

            // ============================================================
            // TOTAL VENUT DIARI
            // ============================================================

            var boto_gridDiaEuro = document.getElementById('gridDiaEuro');

            if (boto_gridDiaEuro) {

                var gridDiaTitle = document.getElementById('gridDiaTitle');

                var valorElement_gridDiaValor =
                    document.getElementById('gridDiaValor');

                var unitatElement_gridDiaUnitat =
                    document.getElementById('gridDiaUnitat');

                var enEuros_gridDiaUnitat = false;

                var valorKwh_gridDiaUnitat = parseFloat(
                    valorElement_gridDiaValor.textContent.trim()
                );

                var valorKwh_gridDiaComplet = valorElement_gridDiaValor.textContent;

                if (!isNaN(valorKwh_gridDiaUnitat)) {

                    boto_gridDiaEuro.addEventListener('click', function () {

                        if (!enEuros_gridDiaUnitat) {

                            gridDiaTitle.innerHTML = "<i class=\"fa-solid fa-coins\"></i>Total venut diari";

                            var euros = valorKwh_gridDiaUnitat * 0.06;

                            valorElement_gridDiaValor.textContent =
                                euros.toFixed(2);
                            unitatElement_gridDiaUnitat.textContent = '€';
                            boto_gridDiaEuro.title = 'Mostrar kWh';
                            enEuros_gridDiaUnitat = true;
                        } else {
                            gridDiaTitle.innerHTML = "<i class=\"fa-solid fa-coins\"></i>Total venut / producció diària";

                            valorElement_gridDiaValor.textContent =
                                valorKwh_gridDiaComplet;
                            unitatElement_gridDiaUnitat.textContent = 'kWh';
                            boto_gridDiaEuro.title = 'Convertir a euros';
                            enEuros_gridDiaUnitat = false;
                        }

                    });
                }
            }
			
           // ============================================================
            // TOTAL VENUT MENSUAL
            // ============================================================

            var boto_gridMesEuro = document.getElementById('gridMesEuro');

            if (boto_gridMesEuro) {

                var gridMesTitle = document.getElementById('gridMesTitle');

                var valorElement_gridMesValor =
                    document.getElementById('gridMesValor');

                var unitatElement_gridMesUnitat =
                    document.getElementById('gridMesUnitat');

                var enEuros_gridMesUnitat = false;

                var valorKwh_gridMesUnitat = parseFloat(
                    valorElement_gridMesValor.textContent.trim()
                );

                var valorKwh_gridMesComplet = valorElement_gridMesValor.textContent;

                if (!isNaN(valorKwh_gridMesUnitat)) {

                    boto_gridMesEuro.addEventListener('click', function () {

                        if (!enEuros_gridMesUnitat) {

                            gridMesTitle.innerHTML = "<i class=\"fa-solid fa-money-bill-wave\"></i>Total venut mensual";    

                            var euros = valorKwh_gridMesUnitat * 0.06;

                            valorElement_gridMesValor.textContent =
                                euros.toFixed(2);
                            unitatElement_gridMesUnitat.textContent = '€';
                            boto_gridMesEuro.title = 'Mostrar kWh';
                            enEuros_gridMesUnitat = true;
                        } else {

                            gridMesTitle.innerHTML = "<i class=\"fa-solid fa-money-bill-wave\"></i>Total venut / producció mensual";

                            valorElement_gridMesValor.textContent =
                                valorKwh_gridMesComplet;
                            unitatElement_gridMesUnitat.textContent = 'kWh';
                            boto_gridMesEuro.title = 'Convertir a euros';
                            enEuros_gridMesUnitat = false;
                        }

                    });
                }
            }

              // ---------------------------------------------------------
        });

    </script>

</body>

</html>
