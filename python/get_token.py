import os
import requests
import hashlib
from clientcode import vars

def guardar_token_en_fitxer(token, nom_fitxer="/home/pi/deyecloud/python/clientcode/variable.py"):
    """
    Genera un fitxer amb la configuració de la baseurl, el token i les capçaleres.
    """
    directori = os.path.dirname(nom_fitxer)
    if directori and not os.path.exists(directori):
        os.makedirs(directori, exist_ok=True)

    contingut = (
        'baseurl = "https://eu1-developer.deyecloud.com/v1.0"\n'
        "token = '{}'\n".format(token) +
        "headers = {\n"
        "    'Content-Type': 'application/json',\n"
        "    'Authorization': 'bearer ' + token\n"
        "}\n"
    )
    
    try:
        with open(nom_fitxer, "w", encoding="utf-8") as fitxer:
            fitxer.write(contingut)
        print("Fitxer '{}' generat correctament amb el nou token.".format(nom_fitxer))
    except Exception as e:
        print("S'ha produït un error al guardar el fitxer: {}".format(e))

if __name__ == '__main__':
    appId = 'xxxxxxxxxxxxxxxxxxx'
    url = vars.baseurl + '/account/token?appId=' + appId
    headers = {
        'Content-Type': 'application/json'
    }
    password = 'xxxxxx'
    sha256_hash = hashlib.sha256()
    sha256_hash.update(password.encode('utf-8'))
    passwordWith256 = sha256_hash.hexdigest()
    
    data = {
        "appSecret": "xxxxxxxxxxxxx",
        "email": "xxxxxxxxxxxxxxxxxxxx",
        "companyId": "0",
        "password": passwordWith256
    }
    
    try:
        response = requests.post(url, headers=headers, json=data)
        response.raise_for_status()

        resultat = response.json()
        print("Resposta de l'API:", resultat)

        token_obtingut = resultat.get('accessToken') 
        
        if token_obtingut:
            guardar_token_en_fitxer(token_obtingut)
        else:
            print("No s'ha trobat el token a la resposta de l'API. Revisa l'estructura JSON.")

    except requests.exceptions.HTTPError as err:
        print("HTTP error occurred: {}".format(err))
    except Exception as err:
        print("Other error occurred: {}".format(err))
