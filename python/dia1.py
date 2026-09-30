import sys
import requests
from clientcode import variable
from datetime import datetime, timedelta

if __name__ == '__main__':

    url = variable.baseurl + '/station/history'
    headers = variable.headers

    avui = datetime.now().date()
    ahir = avui - timedelta(days=1)

    parametre = sys.argv[1] if len(sys.argv) > 1 else None

    # ==========================================================
    # AHIR - resum diari
    # ==========================================================
    if parametre == "-1":

        data_inici = ahir.strftime("%Y-%m-%d")
        data_fi = avui.strftime("%Y-%m-%d")

        data = {
            "stationId": 62563444,
            "granularity": 2,
            "startAt": data_inici,
            "endAt": data_fi
        }

    # ==========================================================
    # AVUI - dades intradia
    # ==========================================================
    else:

        data_inici = avui.strftime("%Y-%m-%d")
        data_fi = avui.strftime("%Y-%m-%d")

        data = {
            "stationId": 62563444,
            "granularity": 1,
            "startAt": data_inici,
            "endAt": data_fi
        }

    #print("PETICIO:")
    #print(data)

    response = requests.post(url, headers=headers, json=data)

    #print("HTTP:", response.status_code)

    resultat = response.json()

    print(resultat)

    # Si hi ha error, acabem
    if not resultat.get("success"):
        sys.exit(1)

    # ==========================================================
    # AVUI
    # ==========================================================
    if parametre != "-1":

        items = resultat.get("stationDataItems", [])

        print("Nombre de mostres:", len(items))

        if not items:
            print("No hi ha dades.")
            sys.exit(0)

        items.sort(key=lambda x: x["timeStamp"])

        produccio_kwh = 0.0
        venut_kwh = 0.0
        comprat_kwh = 0.0

        for i in range(len(items) - 1):

            mostra = items[i]

            timestamp_actual = mostra["timeStamp"]
            timestamp_seguent = items[i + 1]["timeStamp"]

            hores = (timestamp_seguent - timestamp_actual) / 3600.0

            produccio_kwh += (mostra.get("generationPower") or 0) * hores / 1000.0

            venut_kwh += (mostra.get("gridPower") or 0) * hores / 1000.0

            comprat_kwh += (mostra.get("purchasePower") or 0) * hores / 1000.0

        print("")
        print("Produccio avui: %.2f kWh" % produccio_kwh)
        print("Venut avui: %.2f kWh" % abs(venut_kwh))
        print("Comprat avui: %.2f kWh" % comprat_kwh)
