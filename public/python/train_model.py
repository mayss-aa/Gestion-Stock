import pandas as pd
import numpy as np
import os
import joblib
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_squared_error, r2_score
from sklearn.preprocessing import StandardScaler

# ✅ 1. Charger les données
base_dir = os.path.dirname(os.path.abspath(__file__))
file_path = os.path.join(base_dir, "depot_data_final.xlsx")
data = pd.read_excel(file_path)

# ✅ 2. Suppression des valeurs manquantes
data = data.dropna()

# ✅ 3. Remplacement des valeurs 0 dans `taux_augmentation` par le 60e percentile
adjusted_taux = data.loc[data['taux_augmentation'] > 0, 'taux_augmentation'].quantile(0.60)
data['taux_augmentation'] = data['taux_augmentation'].replace(0, adjusted_taux)

# ✅ 4. Ajout d’une nouvelle variable `capacite_utilisee_ratio`
data['capacite_utilisee_ratio'] = data['utilisation_actuelle'] / data['capacite_depot']

# ✅ 5. Vérification de la distribution après correction
plt.figure(figsize=(12, 4))
sns.histplot(data['taux_augmentation'], bins=30, kde=True, color="blue")
plt.title("Distribution du taux d'augmentation après correction avec le 60e percentile")
plt.xlabel("Taux d'augmentation")
plt.ylabel("Fréquence")
plt.show()

# ✅ 6. Définition des variables
X = data[['utilisation_actuelle', 'taux_augmentation', 'capacite_depot', 'capacite_utilisee_ratio']].values
y = (data['capacite_depot'] - data['utilisation_actuelle']) / data['taux_augmentation']

# ✅ 7. Suppression des valeurs infinies et remplacement des NaN par la médiane
y = np.where(np.isinf(y), np.nan, y)
y = np.nan_to_num(y, nan=np.nanmedian(y))

# ✅ 8. Transformation `sqrt(y + 1)` pour éviter les problèmes de zéro et négatifs
y = np.sqrt(y + 1)

# ✅ 9. Normalisation des caractéristiques
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)

# ✅ Sauvegarde du scaler
scaler_path = os.path.join(base_dir, "scaler.pkl")
joblib.dump(scaler, scaler_path)

# ✅ 10. Division des données en train/test
X_train, X_test, y_train, y_test = train_test_split(X_scaled, y, test_size=0.2, random_state=42)

# ✅ 11. Entraînement du modèle avec RandomForestRegressor optimisé
model = RandomForestRegressor(n_estimators=500, max_depth=20, min_samples_split=5, random_state=42)
model.fit(X_train, y_train)

# ✅ 12. Prédictions et correction des valeurs négatives
y_pred = model.predict(X_test)
y_pred = (y_pred ** 2) - 1  # Revenir à l’échelle originale après `sqrt(y + 1)`
y_pred = np.clip(y_pred, 0, None)  # Correction des valeurs négatives

# ✅ 13. Évaluation du modèle
mse = mean_squared_error((y_test ** 2) - 1, y_pred)
r2 = r2_score((y_test ** 2) - 1, y_pred)

print(f"✅ Mean Squared Error (MSE) : {mse:.2f}")
print(f"✅ Coefficient de détermination (R²) : {r2:.4f}")

# ✅ 14. Sauvegarde du modèle
model_path = os.path.join(base_dir, "modele_prevision.pkl")
joblib.dump(model, model_path)

# ✅ 15. Vérification avec Symfony (fichier de test JSON)
result_json = os.path.join(base_dir, "result_prediction.json")
with open(result_json, "w") as f:
    f.write(str(y_pred.tolist()))

# ✅ 16. Affichage du scatter plot amélioré (Prédictions vs Réalités)
plt.figure(figsize=(6, 6))
sns.scatterplot(x=(y_test ** 2) - 1, y=y_pred, color="orange")
plt.plot([min(y_test), max(y_test)], [min(y_test), max(y_test)], linestyle="dashed", color="black")  # Ligne d'identité
plt.xlabel("Valeurs Réelles")
plt.ylabel("Prédictions")
plt.title("Prédictions vs Réalités après correction (RandomForest)")
plt.show()

# ✅ 17. Affichage d’un boxplot pour vérifier la répartition du taux d’augmentation
plt.figure(figsize=(8, 4))
sns.boxplot(x=data['taux_augmentation'], color="blue")
plt.title("Boxplot du taux d'augmentation après correction")
plt.show()
