import sys
import os
import numpy as np
import joblib

# ✅ Forcer l'encodage UTF-8 (Windows fix)
sys.stdout.reconfigure(encoding='utf-8')

# ✅ Définition des chemins des fichiers
base_dir = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(base_dir, "modele_prevision.pkl")
scaler_path = os.path.join(base_dir, "scaler.pkl")

# ✅ Vérification de l'existence des fichiers
if not os.path.exists(model_path) or not os.path.exists(scaler_path):
    print("-1")  # Retourne une valeur d'erreur si les fichiers n'existent pas
    sys.exit(1)

# ✅ Chargement du modèle et du scaler
model = joblib.load(model_path)
scaler = joblib.load(scaler_path)

# ✅ Vérification des arguments reçus
if len(sys.argv) != 4:
    print("-1")  # Retourne une valeur d'erreur si mauvais arguments
    sys.exit(1)

try:
    # ✅ Conversion des arguments en float
    utilisation_actuelle = float(sys.argv[1])
    taux_augmentation = float(sys.argv[2])
    capacite_depot = float(sys.argv[3])

    # ✅ Calcul du ratio d'utilisation
    if capacite_depot <= 0 or taux_augmentation <= 0:
        print("-1")  # Retourne une erreur si les valeurs sont invalides
        sys.exit(1)

    capacite_utilisee_ratio = utilisation_actuelle / capacite_depot

    # ✅ Création de la matrice de features
    X_new = np.array([[utilisation_actuelle, taux_augmentation, capacite_depot, capacite_utilisee_ratio]])

    # ✅ Transformation avec le scaler
    X_new_scaled = scaler.transform(X_new)

    # ✅ Prédiction avec le modèle ML
    y_pred = model.predict(X_new_scaled)

    # ✅ Correction de la prédiction et conversion en jours restants
    jours_restants = max(0, int(round((y_pred[0] ** 2) - 1)))  # Conversion correcte

    # ✅ Retourner UNIQUEMENT la valeur pour Symfony
    print(jours_restants)

except Exception as e:
    print("-1")  # Retourne une erreur en cas de problème
    sys.exit(1)
