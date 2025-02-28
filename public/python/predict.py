import sys

# ✅ Forcer l'encodage UTF-8 (Windows fix)
sys.stdout.reconfigure(encoding='utf-8')

# ✅ Vérification des arguments reçus
if len(sys.argv) != 4:
    print("-1")  # Retourne une valeur d'erreur au lieu d'un message
    sys.exit(1)

try:
    # ✅ Conversion des arguments en nombres
    utilisation_actuelle = float(sys.argv[1])
    taux_augmentation = float(sys.argv[2])
    capacite_depot = float(sys.argv[3])

    # ✅ Calcul du nombre de jours restants avant remplissage
    if taux_augmentation > 0:
        jours_restants = (capacite_depot - utilisation_actuelle) / taux_augmentation
    else:
        jours_restants = -1  # Erreur si pas de croissance

    # ✅ Retourner UNIQUEMENT la valeur numérique pour Symfony
    print(f"{jours_restants:.2f}")  

except ValueError:
    print("-1")  # Retourne une valeur d'erreur pour Symfony
    sys.exit(1)
