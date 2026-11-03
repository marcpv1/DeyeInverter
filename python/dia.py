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
    
    if parametre is not None and int(parametre) < 0:
        
        ahir = avui + timedelta(days=int(parametre))
        
    

    # ==========================================================
    # AVUI
    # ==========================================================
    if parametre is None or int(parametre) > 0:

        data_inici = avui.strftime("%Y-%m-%d")
        data_fi = avui.strftime("%Y-%m-%d")

        data = {
            "stationId": 62563444,
            "granularity": 1,
            "startAt": data_inici,
            "endAt": data_fi
        }

        

    # ==========================================================
    # ANTERIOR
    # ==========================================================
    else:
        
        data_inici = ahir.strftime("%Y-%m-%d")
        data_fi = avui.strftime("%Y-%m-%d")
        
        print(data_inici)
        print(data_fi)

        data = {
            "stationId": 62563444,
            "granularity": 2,
            "startAt": data_inici,
            "endAt": data_fi
        }

        

    #print("PETICIO:")
    #print(data)

    response = requests.post(url, headers=headers, json=data)

    #print("HTTP:", response.status_code)

    resultat = response.json()

    #print(resultat)

    # Si hi ha error, acabem
    if not resultat.get("success"):
        sys.exit(1)

    # ==========================================================
    # AVUI
    # ==========================================================
    if parametre is None or int(parametre) > 0:

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
        
        
        
        
    else:
        #print(resultat)

        items = resultat.get("stationDataItems", [])

        if not items:
            print("No hi ha dades.")
            sys.exit(0)

        mostra = items[0]

        print("")
        print("Produccio avui: %.2f kWh" % (mostra.get("generationValue") or 0))
        print("Venut avui: %.2f kWh" % abs(mostra.get("gridValue") or 0))
        print("Comprat avui: %.2f kWh" % (mostra.get("purchaseValue") or 0))
