# python.py
import sys
import numpy as np
from sklearn.linear_model import LogisticRegression
from sklearn.preprocessing import StandardScaler

try:
    # Check arguments
    if len(sys.argv) != 4:
        print("Error: Please provide attendance, marks, and study_hours")
        sys.exit(1)

    attendance = float(sys.argv[1])
    marks = float(sys.argv[2])
    study_hours = float(sys.argv[3])

    # Sample dataset (demo purpose)
    X = np.array([
        [90, 90, 5],
        [85, 80, 4],
        [80, 75, 3],
        [70, 65, 2],
        [60, 55, 2],
        [50, 45, 1],
        [40, 35, 1],
        [30, 25, 1]
    ])

    y = np.array([1,1,1,1,0,0,0,0])

    # Scale data
    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)

    # Train model
    model = LogisticRegression()
    model.fit(X_scaled, y)

    # Transform input
    input_data = scaler.transform([[attendance, marks, study_hours]])

    # Predict
    prediction = model.predict(input_data)[0]
    probability = model.predict_proba(input_data)[0][1]

    # Output (better format for PHP)
    result = "Pass" if prediction == 1 else "Fail"

    print(f"{result}|{round(probability, 2)}")

except Exception as e:
    print("Error:", str(e))