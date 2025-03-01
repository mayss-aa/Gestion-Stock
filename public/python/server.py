from flask import Flask, request, jsonify
import subprocess

app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # ✅ Récupérer les données envoyées par Symfony
        data = request.json
        utilisation = str(data.get("utilisation_actuelle", "0"))
        taux = str(data.get("taux_augmentation", "0"))
        capacite = str(data.get("capacite_depot", "0"))

        # ✅ Exécuter le script Python `predict.py`
        script_path = "predict.py"
        process = subprocess.run(
            ["python", script_path, utilisation, taux, capacite],
            capture_output=True,
            text=True
        )

        # ✅ Vérifier la sortie
        if process.returncode != 0:
            return jsonify({"error": process.stderr}), 500
        
        result = process.stdout.strip()
        return jsonify({"prediction": result})

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host="0.0.0.0")
