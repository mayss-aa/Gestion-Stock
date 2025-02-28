import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LinearRegression
from sklearn.metrics import mean_squared_error
from sklearn.preprocessing import StandardScaler
import joblib
import os
import sklearn
print(f"✅ Version de scikit-learn utilisée pour l'entraînement : {sklearn.__version__}")

# 📌 Définir le chemin absolu du fichier Excel
base_dir = os.path.dirname(os.path.abspath(__file__))
file_path = os.path.join(base_dir, "depot_data_final.xlsx")

# 📌 Vérifier si le fichier Excel existe
if not os.path.exists(file_path):
    raise FileNotFoundError(f"❌ Le fichier {file_path} n'existe pas. Vérifie son emplacement.")

# 📌 Charger la dataset
data = pd.read_excel(file_path)

# 🔍 Vérification des colonnes
print("🔍 Colonnes du dataset :", data.columns.tolist())

# 📌 Nettoyage et Préparation des Données
data = data.dropna()  # 🔹 Supprimer les valeurs manquantes

# 📌 Gestion des cas où `taux_augmentation = 0`
data['taux_augmentation'] = data['taux_augmentation'].replace(0, 0.0001)  # ✅ Évite la division par zéro sans perturber le ML

# 📌 Définition des Variables (Cas Normal)
X = data[['utilisation_actuelle', "taux_augmentation", "capacite_depot"]]
y = (data["capacite_depot"] - data["utilisation_actuelle"]) / data["taux_augmentation"]  # 🔹 Calcul des jours restants

# ✅ Correction : Éviter les valeurs infinies dans `y`
y.replace([float('inf'), float('-inf')], y[y != float('inf')].max(), inplace=True)

# ✅ Correction : Limiter les valeurs aberrantes (max 10 ans de prévision)
y = y.clip(upper=365 * 10)  # Maximum 10 ans

# 📌 Normalisation des caractéristiques
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)

# ✅ Sauvegarde du Scaler pour utilisation dans `predict.py`
scaler_path = os.path.join(base_dir, "scaler.pkl")
joblib.dump(scaler, scaler_path)
print(f"✅ Scaler sauvegardé dans {scaler_path}")

# 📌 Division des Données en Entraînement et Test
X_train, X_test, y_train, y_test = train_test_split(X_scaled, y, test_size=0.2, random_state=42)

# 📌 Entraîner le Modèle Principal
model = LinearRegression()
model.fit(X_train, y_train)

# 📌 Évaluation du Modèle
y_pred = model.predict(X_test)
mse = mean_squared_error(y_test, y_pred)
print(f"✅ Mean Squared Error: {mse:.2f}")

# 📌 Sauvegarde du modèle principal
model_path = os.path.join(base_dir, "modele_prevision.pkl")
joblib.dump(model, model_path)
print(f"✅ Modèle principal entraîné et sauvegardé dans {model_path}")

# 📌 Cas Spécial : Entraîner un modèle SANS `taux_augmentation`
data_sans_taux = data.copy()
data_sans_taux = data_sans_taux[data_sans_taux["taux_augmentation"] == 0.0001]  # Prendre les cas `taux_augmentation = 0`

if not data_sans_taux.empty:  # Vérifier qu'on a bien des exemples à entraîner
    X_sans_taux = data_sans_taux[['utilisation_actuelle', 'capacite_depot']]  # ❌ Exclut `taux_augmentation`
    y_sans_taux = (data_sans_taux["capacite_depot"] - data_sans_taux["utilisation_actuelle"])  # Juste le delta

    # 📌 Division des Données en Entraînement et Test
    X_train_st, X_test_st, y_train_st, y_test_st = train_test_split(X_sans_taux, y_sans_taux, test_size=0.2, random_state=42)

    # 📌 Entraîner le Modèle Secondaire
    model_sans_taux = LinearRegression()
    model_sans_taux.fit(X_train_st, y_train_st)

    # 📌 Évaluation du Modèle Secondaire
    y_pred_st = model_sans_taux.predict(X_test_st)
    mse_st = mean_squared_error(y_test_st, y_pred_st)
    print(f"✅ Mean Squared Error (Modèle sans `taux_augmentation`): {mse_st:.2f}")

    # 📌 Sauvegarde du modèle secondaire
    model_path_st = os.path.join(base_dir, "modele_sans_taux.pkl")
    joblib.dump(model_sans_taux, model_path_st)
    print(f"✅ Modèle secondaire entraîné et sauvegardé dans {model_path_st}")
 