import sys
import requests
from clientcode import variable
from datetime import datetime, timedelta

if __name__ == '__main__':
    url = variable.baseurl + '/station/history'
    headers = variable.headers

    """
    Retrieve history data of the station, supporting interval data queries in frames, days, months, and years.
    The meaning of granularity is as follow:
    If granularity is 1(frame), the field ‘startAt’ should be in format 'yyyy-MM-dd’. Return the data at ‘startAt’ with intervals of frame(Only support power-related data in repsonse).
    If granularity is 2(day), the field ‘startAt’ and 'endAt’should be in format 'yyyy-MM-dd’. Return the data from ‘startAt’ to ‘endAt’(excluded) (up to 31 days) with intervals of one day.
    If granularity is 3(month), the field ‘startAt’ and 'endAt’should be in format 'yyyy-MM’. Return the data for a certain number of months (up to 12 months)with intervals of one month.
    If granularity is 4(year), the field ‘startAt’ and 'endAt’should be in format 'yyyy’. Return the yearly data between ‘startAt’ to ‘endAt’.
    """
    avui = datetime.now().date()
    dema = avui + timedelta(days=1)
    ahir = avui - timedelta(days=1)

    parametre = sys.argv[1] if len(sys.argv) > 1 else None

    if parametre == "-1":
     data_inici = (avui - timedelta(days=1)).strftime("%Y-%m-%d")
     data_fi = (dema - timedelta(days=1)).strftime("%Y-%m-%d")
    else:
     data_inici = avui.strftime("%Y-%m-%d")
     data_fi = dema.strftime("%Y-%m-%d")

    print(data_inici)
    print(data_fi)

    data = {
        "stationId": 62563444,        # Replace with your stationId in deyecloud
        "granularity": 2,           # The granularity of the telemetry data frame
        "startAt": data_inici,    # Start date
        "endAt": data_fi
    }

    response = requests.post(url, headers=headers, json=data)

    print(response.status_code)
    print(response.json())
