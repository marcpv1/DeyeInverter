<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="180">
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
            transition: background-color 0.15s ease;
            cursor: pointer;
        }

        .valor:hover,
        .valor:active {
            background: #333333;
        }

        .valor .botons {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s ease;
        }

        .valor:hover .botons {
            opacity: 1;
            visibility: visible;
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

        /* Botó calendari */
        .boto-calendari {
            position: absolute;
            top: 12px;
            right: 12px;
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

        .boto-calendari:hover {
            background: #444444;
        }

        .botons {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .botons button {
            position: static !important;
            margin: 0;
        }

        .botons:has(> button:only-child) {
            top: auto;
            bottom: 15px;
        }

        /* Modal de progrés AJAX - VISIBLE PER DEFECTE */
        #ajaxLoading {
            display: flex;
            position: fixed;
            z-index: 99999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            align-items: center;
            justify-content: center;
        }

        .ajaxLoadingBox {
            background: #1e1e1e;
            border-radius: 15px;
            padding: 25px 35px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.6);
            font-size: 18px;
        }

        .ajaxLoadingBox i {
            font-size: 35px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

    <!-- MODAL DE PROGRÉS (Actiu d'inici) -->
    <div id="ajaxLoading">
        <div class="ajaxLoadingBox">
            <i class="fa-solid fa-spinner fa-spin"></i>
            <div>Carregant...</div>
        </div>
    </div>

    <div class="contenidor">

        <h1>Inversor Deye</h1>

        <div class="updated" id="updatedText">
            Carregant dades actualitzades...
        </div>

        <!-- PRODUCCIÓ SOLAR ACTUAL -->
        <div class="valor">
            <div class="literal">
                <i class="fa-solid fa-sun"></i>
                Producció solar actual
            </div>
            <span id="generationValor" class="numero">--</span>
            <span class="unitat">W</span>
        </div>

        <!-- CONSUM ACTUAL -->
        <div class="valor">
            <div class="literal">
                <i class="fa-solid fa-house"></i>
                Consum actual
            </div>
            <span id="consumptionValor" class="numero">--</span>
            <span class="unitat">W</span>
        </div>

        <!-- COMPRAT ACTUAL -->
        <div class="valor">
            <div class="literal">
                <i class="fa-solid fa-bolt"></i>
                Comprat actual
            </div>
            <span id="wireValor" class="numero">--</span>
            <span class="unitat">W</span>
        </div>

        <!-- TOTAL COMPRAT DIARI -->
        <div class="valor">
            <div class="literal">
                <i class="fa-solid fa-bag-shopping"></i>
                Total comprat diari
            </div>
            <span id="purchaseDiaValor" class="numero">--</span>
            <span id="purchaseDiaUnitat" class="unitat">kWh</span>
            <div class="botons">
                <button type="button" id="purchaseDiaEuro" class="boto-euro" title="Convertir a euros">
                    <i class="fa-solid fa-euro-sign"></i>
                </button>
            </div>
        </div>

        <!-- TOTAL VENUT / PRODUCCIÓ DIÀRIA -->
        <div class="valor">
            <div id="gridDiaTitle" class="literal">
                <i class="fa-solid fa-coins"></i>Total venut / producció diària
            </div>
            <span id="gridDiaValor" class="numero">--</span>
            <span id="gridDiaUnitat" class="unitat">kWh</span>
            <div class="botons">
                <button type="button" id="gridDiaEuro" class="boto-euro" title="Convertir a euros">
                    <i class="fa-solid fa-euro-sign"></i>
                </button>
            </div>
        </div>

        <!-- TOTAL COMPRAT MENSUAL -->
        <div class="valor">
            <div id="purchaseMesTitle" class="literal">
                <i class="fa-solid fa-cart-shopping"></i>
                Total comprat mensual
            </div>
            <div class="botons">
                <button type="button" id="purchaseMesCalendari" class="boto-calendari" title="Mes anterior kWh">
                    <i class="fa-solid fa-calendar-days"></i>
                </button>
                <button type="button" id="purchaseMesCalendari2" class="boto-calendari" title="Mes anterior €">
                    <i class="fa-solid fa-calendar"></i>
                </button>
                <button type="button" id="purchaseMesEuro" class="boto-euro" title="Mes actual €">
                    <i class="fa-solid fa-euro-sign"></i>
                </button>
            </div>
            <span id="purchaseMesValor" class="numero">--</span>
            <span id="purchaseMesUnitat" class="unitat">kWh</span>
        </div>

        <!-- TOTAL VENUT / PRODUCCIÓ MENSUAL -->
        <div class="valor">
            <div id="gridMesTitle" class="literal">
                <i class="fa-solid fa-money-bill-wave"></i>Total venut / producció mensual
            </div>
            <span id="gridMesValor" class="numero">--</span>
            <span id="gridMesUnitat" class="unitat">kWh</span>
            <div class="botons">
                <button type="button" id="gridMesCalendari" class="boto-calendari" title="Mes anterior kWh">
                    <i class="fa-solid fa-calendar-days"></i>
                </button>
                <button type="button" id="gridMesCalendari2" class="boto-calendari" title="Mes anterior €">
                    <i class="fa-solid fa-calendar"></i>
                </button>
                <button type="button" id="gridMesEuro" class="boto-euro" title="Convertir a euros">
                    <i class="fa-solid fa-euro-sign"></i>
                </button>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>
    <script>
        $(document).ajaxStart(function () {
            $("#ajaxLoading").css("display", "flex");
        });

        $(document).ajaxStop(function () {
            $("#ajaxLoading").css("display", "none");
        });

        var preu_venta = 0.06;
        var preu_compra = 0.1649;


      // ..........
       // Càrrega inicial de dades via AJAX
        function carregarDadesInicials() {
            $.ajax({
                url: "./deye_get_data.php",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $("#updatedText").text("Última actualització " + data.updated);

                    $("#generationValor").text(data.generation).attr("class", "numero " + data.generationClass);
                    $("#consumptionValor").text(data.consumption).attr("class", "numero " + data.consumptionClass);
                    $("#wireValor").text(data.wire).attr("class", "numero " + data.wireClass);

                    $("#purchaseDiaValor").text(data.purchaseDia).attr("class", "numero " + data.purchaseDiaClass);
                    $("#gridDiaValor").text(data.gridDia + " / " + data.generationDia).attr("class", "numero " + data.generationDiaClass);

                    $("#purchaseMesValor").text(data.purchaseMes).attr("class", "numero " + data.purchaseMesClass);
                    $("#gridMesValor").text(data.gridMes + " / " + data.generationMes).attr("class", "numero " + data.generationMesClass);

                    LoadData();
                },
                error: function (xhr, status, error) {
                    console.error("Error carregant dades:", error);
                    alert("Error carregant les dades inicials.");
                }
            });
        }

        function LoadData() {
            // Lògica dels botons de conversió diaris
            var boto_purchaseDiaEuro = document.getElementById('purchaseDiaEuro');
            var boto_gridDiaEuro = document.getElementById('gridDiaEuro');

            if (boto_purchaseDiaEuro && boto_gridDiaEuro && !boto_purchaseDiaEuro.dataset.eventsRegistrats && !boto_gridDiaEuro.dataset.eventsRegistrats) {
                var valorElement_purchaseDiaValor = document.getElementById('purchaseDiaValor');
                var unitatElement_purchaseDiaUnitat = document.getElementById('purchaseDiaUnitat');
                var valorElement_gridDiaValor = document.getElementById('gridDiaValor');
                var unitatElement_gridDiaUnitat = document.getElementById('gridDiaUnitat');
                var gridDiaTitle = document.getElementById('gridDiaTitle');

                if (valorElement_purchaseDiaValor && unitatElement_purchaseDiaUnitat && valorElement_gridDiaValor && unitatElement_gridDiaUnitat && gridDiaTitle) {
                    var valorKwh_purchaseDia = parseFloat(valorElement_purchaseDiaValor.textContent.trim());
                    var valorKwh_gridDia = parseFloat(valorElement_gridDiaValor.textContent.trim());
                    var valorKwh_gridDiaComplet = valorElement_gridDiaValor.textContent;
                    var enEurosDia = false;

                    if (!isNaN(valorKwh_purchaseDia) && !isNaN(valorKwh_gridDia)) {
                        function canviarUnitatsDia() {
                            if (!enEurosDia) {
                                valorElement_purchaseDiaValor.textContent = (valorKwh_purchaseDia * preu_compra).toFixed(2);
                                unitatElement_purchaseDiaUnitat.textContent = '€';
                                boto_purchaseDiaEuro.title = 'Mostrar kWh';

                                valorElement_gridDiaValor.textContent = (valorKwh_gridDia * preu_venta).toFixed(2);
                                unitatElement_gridDiaUnitat.textContent = '€';
                                boto_gridDiaEuro.title = 'Mostrar kWh';

                                gridDiaTitle.innerHTML = '<i class="fa-solid fa-coins"></i> Total venut diari';
                                enEurosDia = true;
                            } else {
                                valorElement_purchaseDiaValor.textContent = valorKwh_purchaseDia;
                                unitatElement_purchaseDiaUnitat.textContent = 'kWh';
                                boto_purchaseDiaEuro.title = 'Convertir a euros';

                                valorElement_gridDiaValor.textContent = valorKwh_gridDiaComplet;
                                unitatElement_gridDiaUnitat.textContent = 'kWh';
                                boto_gridDiaEuro.title = 'Convertir a euros';

                                gridDiaTitle.innerHTML = '<i class="fa-solid fa-coins"></i> Total venut / producció diària';
                                enEurosDia = false;
                            }
                        }

                        boto_purchaseDiaEuro.addEventListener('click', canviarUnitatsDia);
                        boto_gridDiaEuro.addEventListener('click', canviarUnitatsDia);
                        boto_purchaseDiaEuro.dataset.eventsRegistrats = 'true';
                        boto_gridDiaEuro.dataset.eventsRegistrats = 'true';
                    }
                }
            }

            // Lògica dels botons de conversió mensuals (unificats)
            var boto_purchaseMesEuro = document.getElementById('purchaseMesEuro');
            var boto_gridMesEuro = document.getElementById('gridMesEuro');

            if (boto_purchaseMesEuro && boto_gridMesEuro && !boto_purchaseMesEuro.dataset.eventsRegistrats && !boto_gridMesEuro.dataset.eventsRegistrats) {
                var purchaseMesTitle = document.getElementById('purchaseMesTitle');
                var gridMesTitle = document.getElementById('gridMesTitle');
                var valorElement_purchaseMesValor = document.getElementById('purchaseMesValor');
                var unitatElement_purchaseMesUnitat = document.getElementById('purchaseMesUnitat');
                var valorElement_gridMesValor = document.getElementById('gridMesValor');
                var unitatElement_gridMesUnitat = document.getElementById('gridMesUnitat');

                if (purchaseMesTitle && gridMesTitle && valorElement_purchaseMesValor && unitatElement_purchaseMesUnitat && valorElement_gridMesValor && unitatElement_gridMesUnitat) {
                    var valorKwh_purchaseMes = parseFloat(valorElement_purchaseMesValor.textContent.trim());
                    var valorKwh_purchaseMesComplet = valorElement_purchaseMesValor.textContent;
                    var valorKwh_gridMesUnitat = parseFloat(valorElement_gridMesValor.textContent.trim());
                    var valorKwh_gridMesComplet = valorElement_gridMesValor.textContent;
                    var enEurosMes = false;

                    if (!isNaN(valorKwh_purchaseMes) && !isNaN(valorKwh_gridMesUnitat)) {
                        function canviarUnitatsMes() {
                            if (!enEurosMes) {
                                purchaseMesTitle.innerHTML = '<i class="fa-solid fa-cart-shopping"></i> Total comprat mes / Total net mes';
                                var eurosCompratMes = valorKwh_purchaseMes * preu_compra;
                                var eurosVenutMes = valorKwh_gridMesUnitat * preu_venta;
                                var eurosNetMes = eurosCompratMes - eurosVenutMes;

                                valorElement_purchaseMesValor.textContent = eurosCompratMes.toFixed(2) + ' / ' + eurosNetMes.toFixed(2);
                                unitatElement_purchaseMesUnitat.textContent = '€';
                                boto_purchaseMesEuro.title = 'Mostrar kWh';

                                gridMesTitle.innerHTML = '<i class="fa-solid fa-money-bill-wave"></i> Total venut mensual';
                                valorElement_gridMesValor.textContent = eurosVenutMes.toFixed(2);
                                unitatElement_gridMesUnitat.textContent = '€';
                                boto_gridMesEuro.title = 'Mostrar kWh';

                                enEurosMes = true;
                            } else {
                                purchaseMesTitle.innerHTML = '<i class="fa-solid fa-cart-shopping"></i> Total comprat mensual';
                                valorElement_purchaseMesValor.textContent = valorKwh_purchaseMesComplet;
                                unitatElement_purchaseMesUnitat.textContent = 'kWh';
                                boto_purchaseMesEuro.title = 'Convertir a euros';

                                gridMesTitle.innerHTML = '<i class="fa-solid fa-money-bill-wave"></i> Total venut / producció mensual';
                                valorElement_gridMesValor.textContent = valorKwh_gridMesComplet;
                                unitatElement_gridMesUnitat.textContent = 'kWh';
                                boto_gridMesEuro.title = 'Convertir a euros';

                                enEurosMes = false;
                            }
                        }

                        boto_purchaseMesEuro.addEventListener('click', canviarUnitatsMes);
                        boto_gridMesEuro.addEventListener('click', canviarUnitatsMes);
                        boto_purchaseMesEuro.dataset.eventsRegistrats = 'true';
                        boto_gridMesEuro.dataset.eventsRegistrats = 'true';
                    }
                }
            }
        }

      // ..........




        document.getElementById("purchaseMesCalendari").addEventListener("click", carregarCalendari);
        document.getElementById("purchaseMesCalendari2").addEventListener("click", carregarCalendari2);
        document.getElementById("gridMesCalendari").addEventListener("click", carregarCalendari);
        document.getElementById("gridMesCalendari2").addEventListener("click", carregarCalendari2);

        function carregarCalendari() {
            $.ajax({
                url: "./deye_mes.php",
                type: "POST",
                dataType: "json",
                data: { mes: "-1" },
                success: function (data) {
                    $("#purchaseMesTitle").html('<i class="fa-solid fa-cart-shopping"></i> Total comprat mes ant.');
                    $("#gridMesTitle").html('<i class="fa-solid fa-money-bill-wave"></i> Total venut mes ant.');
                    $("#purchaseMesValor").text(data.stationDataItems[0].purchaseValue);
                    $("#gridMesValor").text(data.stationDataItems[0].gridValue);
                    $("#generationMesValor").text(data.stationDataItems[0].generationValue);
                    $("#purchaseMesUnitat").text("kWh");
                    $("#gridMesUnitat").text("kWh");
                },
                error: function (xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                }
            });
        }

        function carregarCalendari2() {
            $.ajax({
                url: "./deye_mes.php",
                type: "POST",
                dataType: "json",
                data: { mes: "-1" },
                success: function (data) {
                    $("#purchaseMesTitle").html('<i class="fa-solid fa-cart-shopping"></i> Total comprat mes ant. / net ant.');
                    $("#gridMesTitle").html('<i class="fa-solid fa-money-bill-wave"></i> Total venut mes ant.');
                    var purchaseMesValor = data.stationDataItems[0].purchaseValue;
                    var gridMesValor = data.stationDataItems[0].gridValue;
                    var purchaseMesEuros = (purchaseMesValor * preu_compra);
                    var gridMesEuros = (gridMesValor * preu_venta);
                    var netEuros = purchaseMesEuros - gridMesEuros;

                    $("#purchaseMesValor").text(purchaseMesEuros.toFixed(2) + " / " + netEuros.toFixed(2));
                    $("#gridMesValor").text(gridMesEuros.toFixed(2));
                    $("#generationMesValor").text(data.stationDataItems[0].generationValue);
                    $("#purchaseMesUnitat").text("€");
                    $("#gridMesUnitat").text("€");
                },
                error: function (xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            carregarDadesInicials();
        });
    </script>
</body>

</html>