from SystemInfo import SystemInfo
import json, sqlite3, time
from datetime import datetime

start_time = time.time()

SYSTEM_NAME = SystemInfo.get_system_name()

SYSTEM_INFORMATION = {
    'cpu': SystemInfo.get_cpu(),
    'memory': SystemInfo.get_memory(),
    'discs': SystemInfo.get_discs(),
    'temperature': SystemInfo.get_temperature(),
    'created_at': f"{datetime.now()}"
}

SYSTEM_INFORMATION_JSON_FORMAT = str(json.dumps(SYSTEM_INFORMATION))

con = sqlite3.connect('monitoramento.db')

# TODO - Sended => char(1)
con.execute('CREATE TABLE IF NOT EXISTS SystemInfo (id INTEGER PRIMARY KEY, info TEXT NOT NULL, sended INTEGER, system TEXT NOT NULL)');

sql_stat = f"INSERT INTO SystemInfo (info, sended, system) VALUES ('{SYSTEM_INFORMATION_JSON_FORMAT}', 0, '{SYSTEM_NAME}')"

con.execute(sql_stat)
con.commit()

print(f"O Programa executou em : {(time.time() - start_time):.03f} segundos")