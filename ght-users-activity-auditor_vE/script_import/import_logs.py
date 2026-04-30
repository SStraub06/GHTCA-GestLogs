import os
import mysql.connector
from datetime import datetime

# ----------------------------
# CONFIGURATION
# ----------------------------

base_paths_postes = {
    "GUEBWILLER": r"\\hcc-pasteur.fr\appz$\AD\GUEBWILLER\COMPUTERS",
    "ENSISHEIM_NEUFBRISACH": r"\\hcc-pasteur.fr\appz$\AD\ENSISHEIM_NEUFBRISACH\COMPUTERS",
    "MUNSTER": r"\\hcc-pasteur.fr\appz$\AD\MUNSTER\COMPUTERS",
    "RIBEAUVILLE": r"\\hcc-pasteur.fr\appz$\AD\RIBEAUVILLE\COMPUTERS",
    "SOULTZ_ISSENHEIM": r"\\hcc-pasteur.fr\appz$\AD\SOULTZ_ISSENHEIM\COMPUTERS",
    "PASTEUR": r"\\hcc-pasteur.fr\DATA-P\PROFILS\_Audit_PC_CL_AutoIt"
}

base_paths_connexions = {
    "GUEBWILLER": r"\\hcc-pasteur.fr\appz$\AD\GUEBWILLER",
    "ENSISHEIM_NEUFBRISACH": r"\\hcc-pasteur.fr\appz$\AD\ENSISHEIM_NEUFBRISACH",
    "MUNSTER": r"\\hcc-pasteur.fr\appz$\AD\MUNSTER",
    "RIBEAUVILLE": r"\\hcc-pasteur.fr\appz$\AD\RIBEAUVILLE",
    "SOULTZ_ISSENHEIM": r"\\hcc-pasteur.fr\appz$\AD\SOULTZ_ISSENHEIM",
    "PASTEUR": r"\\hcc-pasteur.fr\DATA-P\PROFILS\_Audit_PC_CL"
}

sites = {
    "GUEBWILLER": 1,
    "ENSISHEIM_NEUFBRISACH": 2,
    "MUNSTER": 3,
    "RIBEAUVILLE": 4,
    "SOULTZ_ISSENHEIM": 5,
    "PASTEUR": 6
}

# ----------------------------
# DB CONNECTION
# ----------------------------

# Connexion en local

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    database="ght-gestionlogs"
)

#conn = mysql.connector.connect(
    #$host = "wsspr01.hcc-pasteur.fr";
    #$dbname = "ght-gestionlogs";
    #$username = "root";
    #$password = "*6clone*";
#)



cursor = conn.cursor()

# ----------------------------
# CLEAN
# ----------------------------

def clean(value):
    if value is None:
        return None

    value = value.strip().replace("\n", "").replace("\r", "")

    if value == "" or value.lower() == "absent":
        return None

    return value

# ----------------------------
# PARSE DATE
# ----------------------------

def parse_date(value):
    value = clean(value)

    if not value:
        return None

    try:
        return datetime.strptime(value, "%Y/%m/%d %H:%M:%S")
    except:
        return None

# ----------------------------
# READ LOG
# ----------------------------

def lire_log(filepath):
    data = {}

    try:
        with open(filepath, encoding="utf-8") as f:
            for line in f:
                if ":" in line:
                    key, value = line.split(":", 1)
                    data[key.strip()] = value.strip()

    except Exception as e:
        print("Erreur lecture :", filepath, e)

    return data

# ----------------------------
# IMPORT POSTES
# ----------------------------

def import_postes():

    print("Import POSTES...")

    for site, path in base_paths_postes.items():

        id_site = sites[site]

        if not os.path.exists(path):
            print("Dossier introuvable :", path)
            continue

        for file in os.listdir(path):

            if not file.endswith(".txt"):
                continue

            filepath = os.path.join(path, file)
            data = lire_log(filepath)

            nom_poste = clean(data.get("Nom du poste"))

            if not nom_poste:
                continue

            try:

                cursor.execute("""

                INSERT INTO postes(
                    nom_poste,
                    adresse_mac,
                    type_disque,
                    ram_gio,
                    cpu_model,
                    cpu_freq_ghz,
                    os_version,
                    os_build,
                    os_arch,
                    version_firefox,
                    version_chrome,
                    version_internet_explorer,
                    version_dotnet,
                    version_client_citrix,
                    version_edictee,
                    version_cws,
                    version_philips_speech_drivers,
                    version_dragon,
                    version_office,
                    version_trend_micro,
                    version_cryptolib,
                    id_site
                )

                VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)

                ON DUPLICATE KEY UPDATE
                    adresse_mac = COALESCE(VALUES(adresse_mac), adresse_mac),
                    type_disque = COALESCE(VALUES(type_disque), type_disque),
                    ram_gio = COALESCE(VALUES(ram_gio), ram_gio),
                    cpu_model = COALESCE(VALUES(cpu_model), cpu_model),
                    cpu_freq_ghz = COALESCE(VALUES(cpu_freq_ghz), cpu_freq_ghz),
                    os_version = COALESCE(VALUES(os_version), os_version),
                    os_build = COALESCE(VALUES(os_build), os_build),
                    os_arch = COALESCE(VALUES(os_arch), os_arch),
                    version_firefox = COALESCE(VALUES(version_firefox), version_firefox),
                    version_chrome = COALESCE(VALUES(version_chrome), version_chrome),
                    version_internet_explorer = COALESCE(VALUES(version_internet_explorer), version_internet_explorer),
                    version_dotnet = COALESCE(VALUES(version_dotnet), version_dotnet),
                    version_client_citrix = COALESCE(VALUES(version_client_citrix), version_client_citrix),
                    version_edictee = COALESCE(VALUES(version_edictee), version_edictee),
                    version_cws = COALESCE(VALUES(version_cws), version_cws),
                    version_philips_speech_drivers = COALESCE(VALUES(version_philips_speech_drivers), version_philips_speech_drivers),
                    version_dragon = COALESCE(VALUES(version_dragon), version_dragon),
                    version_office = COALESCE(VALUES(version_office), version_office),
                    version_trend_micro = COALESCE(VALUES(version_trend_micro), version_trend_micro),
                    version_cryptolib = COALESCE(VALUES(version_cryptolib), version_cryptolib)

                """, (

                    nom_poste,
                    clean(data.get("Adresse MAC")),
                    clean(data.get("Type de Disque Dur")),
                    int(round(float(clean(data.get("Taille de la RAM (Gio)"))))) if clean(data.get("Taille de la RAM (Gio)")) else None,
                    clean(data.get("CPU Model")),
                    float(clean(data.get("CPU Frequence (GHz)"))) if clean(data.get("CPU Frequence (GHz)")) else None,
                    clean(data.get("OS Version")),
                    clean(data.get("OS Build")),
                    clean(data.get("OS 32 ou 64 Bits")),
                    clean(data.get("Version de Firefox")),
                    clean(data.get("Version de Chrome")),
                    clean(data.get("Version de Internet Explorer")),
                    clean(data.get("Version de DotNet")),
                    clean(data.get("Version du Client Citrix")),
                    clean(data.get("Version de eDictee")),
                    clean(data.get("Version de CWS")),
                    clean(data.get("Version de Philips Speech Drivers")),
                    clean(data.get("Version de Dragon")),
                    clean(data.get("Version de Office")),
                    clean(data.get("Version de Trend Micro")),
                    clean(data.get("Version de Cryptolib")),
                    id_site
                ))

            except Exception as e:
                print("Erreur poste :", nom_poste, e)

# ----------------------------
# IMPORT CONNEXIONS
# ----------------------------

def import_connexions():

    print("Import CONNEXIONS...")

    for site, path in base_paths_connexions.items():

        if not os.path.exists(path):
            print("Dossier introuvable :", path)
            continue

        for file in os.listdir(path):

            if not file.endswith(".txt"):
                continue

            if file == "COMPUTERS":
                continue

            filepath = os.path.join(path, file)
            data = lire_log(filepath)

            nom_poste_brut = clean(data.get("Nom du poste"))

            if not nom_poste_brut:
                continue

            nom_poste = nom_poste_brut.split(".")[0]

            cursor.execute(
                "SELECT id FROM postes WHERE nom_poste=%s",
                (nom_poste,)
            )

            result = cursor.fetchone()

            if not result:
                continue

            id_poste = result[0]

            date_connexion = parse_date(data.get("Date de connexion"))
            date_deconnexion = parse_date(data.get("Date de deconnexion"))

            utilisateur = clean(data.get("Nom utilisateur"))

            # 🔥 ANTI DOUBLON
            cursor.execute("""
                SELECT id FROM connexions
                WHERE id_poste=%s
                AND nom_utilisateur=%s
                AND (
                    (date_connexion = %s)
                    OR (date_connexion IS NULL AND %s IS NULL)
                )
            """, (id_poste, utilisateur, date_connexion, date_connexion))

            if cursor.fetchone():
                continue

            try:

                cursor.execute("""
                INSERT INTO connexions(
                    id_poste,
                    nom_utilisateur,
                    adresse_ip,
                    serveur_auth,
                    date_connexion,
                    date_deconnexion,
                    uptime,
                    imprimante_defaut,
                    liste_imprimantes,
                    lecteurs_reseaux,
                    temps_execution_script
                )
                VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)
                """, (

                    id_poste,
                    utilisateur,
                    clean(data.get("Adresse IP")),
                    clean(data.get("Nom du serveur qui a fait l'authentification")),
                    date_connexion,
                    date_deconnexion,
                    clean(data.get("Uptime")),
                    clean(data.get("Nom de l'imprimante par defaut")),
                    clean(data.get("Liste des imprimantes")),
                    clean(data.get("Lecteurs reseaux")),
                    clean(data.get("Temps d'execution du script"))
                ))

            except Exception as e:
                print("Erreur connexion :", file, e)

# ----------------------------
# EXECUTION
# ----------------------------

import_postes()
import_connexions()

conn.commit()
conn.close()

print("✅ Import terminé sans doublons")