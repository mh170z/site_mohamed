function saveChanges(data, index) {
  const row = data.rows[index];
  if (row) {
    data.columns.forEach((column) => {
      let input = document.getElementById(`${data.name}-${column}-${index}`);
      if (input) {
        row[column] = input.value;
      }
    });
    if (data.name == "ACTIVITE") {
      const column = "DATEANNULEACT";
      let input = document.getElementById(`${data.name}-${column}-${index}`);
      if (row["CODEETATACT"] == "AN") {
        row[column] = today().substring(0, 10);
      } else {
        row[column] = "";
      }
      input.value = row[column];
    }
  }
}

function createNew(data, rows, modifie, index) {
  let rowData = { __new: true };
  data.columns.forEach((column) => {
    rowData[column] = undefined;
  });

  saveChanges(data, index); // sauvegarde les modifications actuelles
  data.rows.splice(index, 0, rowData); // insère la nouvelle ligne rowdata
  rows.splice(index, 0, { __new: true });
  modifie.splice(index, 0, true);
  return rowData;

  //console.log(currentRowIndex, JSON.stringify(modifie));
}

function today() {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0"); // Mois = 0-indexé
  const dd = String(today.getDate()).padStart(2, "0");
  const hh = String(today.getHours()).padStart(2, "0");
  const min = String(today.getMinutes()).padStart(2, "0");
  const ss = String(today.getSeconds()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;
}

function loadTable(tableName, callback) {
  // Envoie simplement les données de l'inscription au serveur
  fetch("load_table.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ tablename: tableName }), // Passer le nom de la table à charger
  })
    .then((response) => response.json())
    .then((result) => {
      //console.log(`result: ${JSON.stringify(result)}`);
      if (result.success) {
        callback(result.success);
      } else {
        alert(`load_table à échouée - ${result.error}`);
      }
    });
}

// fontion pour gérer une table (ie. ACTIVITE, ANIMATION, COMPTE, ...)
function createTableContainer(userId, tableName, container, titre, filtre) {
  loadTable(tableName, (data) => {
    createContainer(userId, data, tableName, container, titre, filtre);
  });
}

// fontion pour gérer une table (ie. ACTIVITE, ANIMATION, COMPTE, ...)
function createContainer(
  userId,
  tableData,
  tableName,
  container,
  titre,
  filtre
) {
  let currentRowIndex = 0;

  let insertMode = false;

  let data = filtre
    ? {
        ...tableData,
        rows: tableData.rows.filter(filtre), // applique le filtre
      }
    : tableData;

  if (data.rows.length > 0 && tableName == "ANIMATION") {
    codeAnim = data.rows[0]["CODEANIM"];
    data.rows = [...tableData.rows];
    currentRowIndex = data.rows.findIndex((row) => row.CODEANIM === codeAnim);
  }

  const rows = data.rows.map((row) => ({ ...row }));
  const createNewRow = false;

  container.classList.add("table-manage");

  let modifie = data.rows.map((_) => false);

  // Fonction pour marquer la table comme modifiée en changeant la class du titre
  function setTitreClass() {
    let titleElement = document.getElementById(`${tableName}-titre`);
    // Change la classe
    if (modifie[currentRowIndex]) {
      titleElement.classList.remove("titre");
      titleElement.classList.add("titreModifie");
    } else {
      titleElement.classList.remove("titreModifie");
      titleElement.classList.add("titre");
    }
  }

  function tableModifie() {
    const columns = Object.keys(data.columnTypes);

    //console.log(JSON.stringify(columns));

    // Vérifier si au moins un champ a été modifié
    modifie[currentRowIndex] =
      rows[currentRowIndex]["__new"] ||
      columns.some((column, i) => {
        const input = document.getElementById(
          `${tableName}-${column}-${currentRowIndex}`
        );
        if (!input) return false; // Évite les erreurs si l'élément n'existe pas

        const newValue = input.value ?? ""; // Valeur du champ
        const oldValue = rows[currentRowIndex][column] ?? ""; // Valeur originale

        //console.log(`${column}_${currentRowIndex} "${newValue}" !== "${oldValue}" ${newValue !== oldValue}`);

        return newValue !== oldValue; // Retourne vrai si différent
      });

    saveChanges(data, currentRowIndex);

    setTitreClass();
  }

  function handleInscription(row) {
    // Envoie simplement les données de l'inscription au serveur
    fetch("inscription.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ row }), // Passer l'ensemble de l'objet row
    })
      .then((response) => response.json())
      .then((result) => {
        //console.log(`result: ${JSON.stringify(result)}`);
        if (result.success) {
          alert(result.success);
        } else {
          alert(`L'inscription à échouée - ${result.error}`);
        }
      })
      .catch((error) => {
        alert(`Error: ${error}`);
      });
  }

  function createCaptionAnimation(caption, tableName, row, titre, insertMode) {
    const wrapper = document.createElement("div");
    wrapper.style.display = "flex";
    wrapper.style.flexDirection = "column"; // empile les enfants verticalement

    const titreElement = document.createElement("strong");
    titreElement.id = `${tableName}-titre`;
    titreElement.className = "titre";
    titreElement.textContent = titre;

    if (!insertMode) {
      // wrapper pour aligner le lien à droite
      const lienWrapper = document.createElement("div");
      lienWrapper.style.display = "flex";
      lienWrapper.style.justifyContent = "flex-end"; // pousse à droite

      const lien = document.createElement("a");
      lien.href = "#";
      lien.textContent = "Liste des activités";
      lien.classList.add("lien");

      const codeAnim = row["CODEANIM"];
      const nomAnim = row["NOMANIM"];

      lien.addEventListener("click", (e) => {
        e.preventDefault();
        show(
          "ACTIVITE",
          `Activités de l'animation ${codeAnim} (${nomAnim})`,
          (row) => {
            return row.CODEANIM === codeAnim;
          }
        );
      });

      lienWrapper.appendChild(lien);
      wrapper.appendChild(lienWrapper);
    }

    wrapper.appendChild(titreElement);
    caption.appendChild(wrapper);
  }

  function createCaptionActivite(caption, tableName, row, titre, insertMode) {
    const wrapper = document.createElement("div");
    wrapper.style.display = "flex";
    wrapper.style.flexDirection = "column"; // empile les enfants verticalement

    const titreElement = document.createElement("strong");
    titreElement.id = `${tableName}-titre`;
    titreElement.className = "titre";
    titreElement.textContent = titre;

    if (!insertMode) {
      // wrapper pour aligner le lien à droite
      const lienWrapper = document.createElement("div");
      lienWrapper.style.display = "flex";
      lienWrapper.style.justifyContent = "flex-end"; // pousse à droite

      const codeAnim = row["CODEANIM"];
      const dateAct = row["DATEACT"];

      const lienAnim = document.createElement("a");
      lienAnim.href = "#";
      lienAnim.textContent = "animation";
      lienAnim.classList.add("lien");

      lienAnim.addEventListener("click", (e) => {
        e.preventDefault();
        show("ANIMATION", "Animation", (row) => {
          return row.CODEANIM === codeAnim;
        });
      });

      const lienListInscription = document.createElement("a");
      lienListInscription.href = "#";
      lienListInscription.textContent = "list des inscriptions";
      lienListInscription.classList.add("lien");

      lienListInscription.addEventListener("click", (e) => {
        e.preventDefault();
        show(
          "INSCRIPTION",
          `Inscriptions de l'animation ${codeAnim} à la date ${dateAct}`,
          (row) => {
            return row.CODEANIM === codeAnim && row.DATEACT == dateAct;
          }
        );
      });

      const lienInscription = document.createElement("a");
      lienInscription.href = "#";
      lienInscription.textContent = "s'inscrire";
      lienInscription.classList.add("lien");

      lienInscription.addEventListener("click", (e) => {
        e.preventDefault();

        if (row["CODEETATACT"] != "AN") {
          handleInscription({
            USER: userId,
            CODEANIM: codeAnim,
            DATEACT: dateAct,
            DATEINSCRIP: today(),
            HRDEBUTACT: row.HRDEBUTACT,
            HRFINACT: row.HRFINACT,
          });
        } else alert("Activité annulée");
      });

      lienWrapper.appendChild(lienAnim);
      lienWrapper.appendChild(lienListInscription);
      lienWrapper.appendChild(lienInscription);
      wrapper.appendChild(lienWrapper);
    }

    wrapper.appendChild(titreElement);
    caption.appendChild(wrapper);
  }

  function createCaptionInscription(
    caption,
    tableName,
    row,
    titre,
    insertMode
  ) {
    const wrapper = document.createElement("div");
    wrapper.style.display = "flex";
    wrapper.style.flexDirection = "column"; // empile les enfants verticalement

    // Première ligne : le titre (aligné à gauche par défaut)
    const titreElement = document.createElement("strong");
    titreElement.id = `${tableName}-titre`;
    titreElement.className = "titre";
    titreElement.textContent = titre;

    // Deuxième ligne : wrapper pour aligner le lien à droite
    const lienWrapper = document.createElement("div");
    lienWrapper.style.display = "flex";
    lienWrapper.style.justifyContent = "flex-end"; // pousse à droite

    if (!insertMode) {
      const lien = document.createElement("a");
      lien.href = "#";
      lien.textContent = "activité";
      lien.classList.add("lien");

      const codeAnim = row["CODEANIM"];
      const dateAct = row["DATEACT"];

      lien.addEventListener("click", (e) => {
        e.preventDefault();
        show(
          "ACTIVITE",
          `Activités de l'inscription NOINSCRIP=${row.NOINSCRIP}`,
          (row) => {
            return row.CODEANIM === codeAnim && row.DATEACT == dateAct;
          }
        );
      });

      lienWrapper.appendChild(lien);
      wrapper.appendChild(lienWrapper);
    }

    wrapper.appendChild(titreElement);
    caption.appendChild(wrapper);
  }

  function createCaption(tableName, row, titre, insertMode) {
    let caption = document.createElement("caption");

    if (tableName === "ANIMATION") {
      createCaptionAnimation(caption, tableName, row, titre, insertMode);
    } else if (tableName === "ACTIVITE") {
      createCaptionActivite(caption, tableName, row, titre, insertMode);
    } else if (tableName === "INSCRIPTION") {
      createCaptionInscription(caption, tableName, row, titre, insertMode);
    } else {
      caption.innerHTML = `<strong id="${tableName}-titre" class="titre">${titre}</strong>`;
    }

    /* 
    const titreElement = document.getElementById(`${tableName}-titre`);

    setTimeout(() => (titreElement.textContent = titre), 2000);*/

    caption.classList.add("table-title"); // Ajout de la classe CSS

    return caption;
  }

  // Fonction pour afficher une seule ligne de la table en fonction de l'indice
  function renderRow(index) {
    container.innerHTML = "";

    let row;
    if (data.rows[index]) {
      row = data.rows[index];
    } else {
      row = createNew(data, rows, modifie, 0);
      insertMode = true;
    }

    // Créer le tableau pour afficher le nom du champ et le champ de saisie pour chaque colonne
    let tableElement = document.createElement("table");
    tableElement.classList.add("data-table");
    tableElement.style.width = "100%";
    tableElement.style.borderCollapse = "collapse";

    tableElement.appendChild(
      createCaption(data.name, data.rows[index], titre, insertMode)
    );

    let note = document.createElement("strong");
    note.classList.add("note");
    note.innerHTML = "* modifiable, ^ clé primaire, ! obligatoire";

    data.columns.forEach((column) => {
      let rowElement = document.createElement("tr");

      let fieldNameCell = document.createElement("td");
      fieldNameCell.textContent = column;
      fieldNameCell.style.border = "1px solid #ddd";
      fieldNameCell.style.padding = "8px";

      if (userRole === "AD" || userRole === "EN") {
        fieldNameCell.classList.add("modifiable");
      } else {
        fieldNameCell.classList.add("non-modifiable");
      }

      if (data.primaryKeys.includes(column)) {
        fieldNameCell.classList.add("primary-key");
      }

      if (!data.nullable[column]) {
        fieldNameCell.classList.add("obligatoire");
      }

      let fieldValueCell = document.createElement("td");
      let input;
      let columnType =
        data.columnTypes && data.columnTypes[column]
          ? data.columnTypes[column]
          : "text";

      if (data.fkValues[column]) {
        // la colonne est un foreign key; crée une liste de valeurs possible pour la colonne
        input = document.createElement("select");
        input.style.width = "100%";
        input.style.padding = "8px";
        input.style.border = "1px solid #ddd";

        let emptyOption = document.createElement("option");
        emptyOption.value = "";
        emptyOption.textContent = "-- Select --";
        input.appendChild(emptyOption);

        data.fkValues[column].forEach((value) => {
          let option = document.createElement("option");
          option.value = value;
          option.textContent = value;
          if (row && row[column] === value) {
            option.selected = true;
          }
          input.appendChild(option);
        });
      } else {
        if (tableName == "COMPTE" && column == "MDP") {
          input = document.createElement("input");
          input.type = "password";
          input.style.width = "100%";
        } else {
          if (columnType.includes("char") || columnType.includes("varchar")) {
            const match = columnType.match(/\d+/);
            if (match && match[0] > 40) {
              input = document.createElement("textarea");
              input.style.width = "100%";
              input.style.height = "60px";
            } else {
              input = document.createElement("input");
              input.type = "text";
              input.style.width = "100%";
            }
          } else if (
            columnType.includes("int") ||
            columnType.includes("float") ||
            columnType.includes("double") ||
            columnType.includes("decimal")
          ) {
            input = document.createElement("input");
            input.type = "number";
            input.style.width = "100%";
          } else if (columnType.includes("datetime")) {
            input = document.createElement("input");
            input.type = "datetime-local";
            input.style.width = "100%";
          } else if (columnType.includes("date")) {
            input = document.createElement("input");
            input.type = "date";
            input.style.width = "100%";
          } else if (columnType.includes("time")) {
            input = document.createElement("input");
            input.type = "time";
            input.style.width = "100%";
          } else {
            input = document.createElement("input");
            input.type = "text";
            input.style.width = "100%";
          }
        }

        if (row) {
          input.value = row[column] ? row[column] : "";
        } else {
          input.value = "";
        }
      }

      input.id = `${tableName}-${column}-${index}`;
      input.style.padding = "8px";
      input.style.border = "1px solid #ddd";
      input.style.boxSizing = "border-box";
      input.style.backgroundColor = "#fafafa";
      input.style.transition = "border-color 0.3s ease";

      input.addEventListener(
        "focus",
        () => (input.style.borderColor = "#3498db")
      );
      input.addEventListener("blur", () => (input.style.borderColor = "#ddd"));
      input.addEventListener("input", tableModifie);

      input.disabled = !(userRole === "AD" || userRole === "EN");

      fieldValueCell.style.border = "1px solid #ddd";
      fieldValueCell.style.padding = "8px";
      fieldValueCell.appendChild(input);

      rowElement.appendChild(fieldNameCell);
      rowElement.appendChild(fieldValueCell);
      tableElement.appendChild(rowElement);

      // si la colonne est incrémenter automatiquement en base alors le input n'accepte pas de modification
      input.disabled =
        data.autoIncrement[column] ||
        column == "DATEANNULEACT" ||
        column == "DATEANNULE";

      if (
        (tableName == "INSCRIPTION" && column == "DATEINSCRIP") ||
        (tableName == "ANIMATION" && column == "DATECREATIONANIM") ||
        (tableName == "COMPTE" && column == "DATEINSCRIP")
      ) {
        input.value = today();
        input.disabled = true;
      }
    });

    container.appendChild(note);
    container.appendChild(tableElement);

    // boutons de navigation (Suivant/Précédant)
    let navDiv = document.createElement("div");
    navDiv.classList.add("navigation-buttons");

    let prevButton = document.createElement("button");
    prevButton.textContent = "Précédant";
    prevButton.disabled = currentRowIndex === 0;
    prevButton.onclick = () => {
      saveChanges(data, currentRowIndex);
      if (currentRowIndex > 0) {
        currentRowIndex--;
        renderRow(currentRowIndex);
      }
      setTitreClass();
    };

    let nextButton = document.createElement("button");
    nextButton.textContent = "Suivant";
    nextButton.disabled = currentRowIndex === data.rows.length - 1;
    nextButton.onclick = () => {
      saveChanges(data, currentRowIndex);
      if (currentRowIndex < data.rows.length - 1) {
        currentRowIndex++;
        renderRow(currentRowIndex);
      }
      setTitreClass();
    };

    navDiv.appendChild(prevButton);
    navDiv.appendChild(nextButton);

    if (createNewRow) navDiv.style.display = "none";

    let manageDiv = document.createElement("div");
    manageDiv.classList.add("manage-buttons");
    let updateButton = document.createElement("button");
    updateButton.textContent = "Mise à jour";
    updateButton.classList.add("update-btn");
    //si c'est admin bouton visible si encadran ou vacancier bouton invisible. boutons dynamique
    if (userRole === "AD" || userRole === "EN") {
      updateButton.disabled = false;
      updateButton.style.display = "block"; //rend le bouton visible dans la page.
    } else {
      updateButton.disabled = true; // bouton non utilisable
      updateButton.style.display = "none"; // bouton pas visible
    }

    function update(insert, callback) {
      // insert:Boolean si true insert sinon update
      let missingFields = [];
      data.columns.forEach((column) => {
        let input = document.getElementById(
          `${tableName}-${column}-${currentRowIndex}`
        );
        let value = input.value.trim();

        // Vérifiez si la colonne n'est PAS NULL et a une valeur vide.
        if (!data.nullable[column] && value === "") {
          missingFields.push(column);
        } else {
          let columnType = data.columnTypes[column] || "text";

          if (columnType.includes("int")) {
            value = value ? parseInt(value, 10) : null;
          } else if (
            columnType.includes("decimal") ||
            columnType.includes("float") ||
            columnType.includes("double")
          ) {
            value = value ? parseFloat(value) : null;
          } else if (
            columnType.includes("date") ||
            columnType.includes("time")
          ) {
            value = value || null;
          }
        }
      });

      if (data.name == "INSCRIPTION") {
        missingFields = missingFields.filter((item) => item !== "NOINSCRIP");
      }

      // Si des champs requis sont manquants, afficher une alerte et arrêter la mise à jour.
      missingFields.forEach((field) => {
        let input = document.getElementById(
          `${tableName}-${field}-${currentRowIndex}`
        );
        if (!input) return;

        let border = input.style.border;
        let backgroundColor = input.style.backgroundColor;

        input.style.border = "2px solid red"; // Red border for error
        input.style.backgroundColor = "#ffcccc"; // Light red background

        // enlève le highlight après 1 seconde
        setTimeout(() => {
          input.style.border = border;
          input.style.backgroundColor = backgroundColor;
        }, 1000);
      });

      //console.log(`updatedRow ${JSON.stringify(updatedRow)}`);

      function deleteColonne(row, name) {
        const { [name]: _, ...rest } = row;
        return rest;
      }
      if (missingFields.length == 0) {
        function upsert(row) {
          fetch(insert ? "insert_row.php" : "update_row.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              table: data.name,
              // la colonne NOINSCRIP est incrémenter automatiquement
              // donc il faut l'enlever de la requête INSERT
              row:
                insert && data.name == "INSCRIPTION"
                  ? deleteColonne(row, "NOINSCRIP")
                  : row,
              primaryKeys: data.primaryKeys,
            }),
          })
            .then((response) => response.json())
            .then((result) => {
              //console.log(`result: ${JSON.stringify(result)}`);
              if (result.success) {
                if (insert && data.name == "INSCRIPTION") {
                  row["NOINSCRIP"] = result.NOINSCRIP;
                }
                delete row["__new"]; // enlève le champ __new
                data.rows[currentRowIndex] = row;
                modifie[currentRowIndex] = false;
                setTitreClass();
                alert(result.success);
                if (callback) callback();
              } else {
                alert(
                  `Error ${insert ? "inserting" : "updating"} row: ${
                    result.error
                  }`
                );
                if (callback) callback(result.error);
              }
            })
            .catch((error) => {
              alert(`Error: ${error}`);
              if (callback) callback(error);
            });
        }
        if (tableName === "COMPTE") {
          let input = document.getElementById(
            `${tableName}-MDP-${currentRowIndex}`
          );
          const newMDP = input.value.trim();
          if (newMDP !== rows[currentRowIndex]["MDP"]) {
            confirmNewPassword(newMDP, (hashedPassword, err) => {
              if (err) {
                alert(err);
              } else {
                let row = {};
                data.columns.forEach((column) => {
                  let input = document.getElementById(
                    `${tableName}-${column}-${currentRowIndex}`
                  );
                  let value = input.value.trim();
                  row[column] = value;
                });
                row["MDP"] = hashedPassword;
                upsert(row);
              }
            });
          } else {
            let row = {};
            data.columns.forEach((column) => {
              let input = document.getElementById(
                `${tableName}-${column}-${currentRowIndex}`
              );
              let value = input.value.trim();
              row[column] = value;
            });
            upsert(row);
          }
        } else {
          let row = {};
          data.columns.forEach((column) => {
            let input = document.getElementById(
              `${tableName}-${column}-${currentRowIndex}`
            );
            let value = input.value.trim();
            row[column] = value;
          });
          upsert(row);
        }
      } else {
        if (callback) callback("missing fields");
      }
    }

    updateButton.onclick = () => {
      update();
    };

    let deleteButton = document.createElement("button");
    deleteButton.textContent = "Supprimer";
    deleteButton.classList.add("delete-btn");

    if (userRole === "AD" || userRole === "EN") {
      deleteButton.disabled = false;
      deleteButton.style.display = "block";
    } else {
      deleteButton.disabled = true;
      deleteButton.style.display = "none";
    }

    deleteButton.onclick = () => {
      if (confirm("Êtes-vous sûr de vouloir supprimer cette ligne ?")) {
        var row = data.rows[currentRowIndex];
        if (row["__new"]) {
          // nouveau et pas mis à jour (donc n'existe pas dans la base de donnée)
          data.rows.splice(currentRowIndex, 1);
          modifie.splice(currentRowIndex, 1);
          setTitreClass();
          if (currentRowIndex >= data.rows.length) {
            currentRowIndex = data.rows.length - 1;
          }
          renderRow(currentRowIndex);
        } else {
          fetch("delete_row.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              table: data.name,
              row: data.primaryKeys.reduce((acc, key) => {
                if (row.hasOwnProperty(key)) {
                  acc[key] = row[key];
                }
                return acc;
              }, {}),
            }),
          })
            .then((response) => response.json())
            .then((result) => {
              if (result.success) {
                data.rows.splice(currentRowIndex, 1);
                if (currentRowIndex >= data.rows.length) {
                  currentRowIndex = data.rows.length - 1;
                }
                renderRow(currentRowIndex);
                setTitreClass();
                alert(result.success);
              } else {
                alert(`Error updating row: ${result.error}`);
              }
            })
            .catch((error) => alert(`Error: ${error}`));
        }
      }
    };

    if (createNewRow) deleteButton.style.display = "none";

    let ajouteButton = document.createElement("button");
    ajouteButton.textContent = "Ajouter";
    ajouteButton.classList.add("insert-btn");
    if (userRole === "AD" || userRole === "EN") {
      ajouteButton.disable = false;
      ajouteButton.style.display = "block";
    } else {
      ajouteButton.disable = true;
      ajouteButton.style.display = "none";
    }

    ajouteButton.onclick = () => {
      insertMode = true;
      let rowData = { __new: true };
      data.columns.forEach((column) => {
        rowData[column] = undefined;
      });

      saveChanges(data, currentRowIndex);
      currentRowIndex++;
      data.rows.splice(currentRowIndex, 0, rowData);
      rows.splice(currentRowIndex, 0, { __new: true });
      modifie.splice(currentRowIndex, 0, true);

      //console.log(currentRowIndex, JSON.stringify(modifie));

      container.innerHTML = "";
      renderRow(currentRowIndex);
      setTitreClass();
    };

    if (createNewRow) ajouteButton.style.display = "none";

    manageDiv.appendChild(updateButton);
    manageDiv.appendChild(deleteButton);
    manageDiv.appendChild(ajouteButton);

    if (insertMode) {
      let insertDiv = document.createElement("div");
      insertDiv.classList.add("insert-buttons");
      let insertButton = document.createElement("button");
      let cancelButton = document.createElement("button");
      insertButton.textContent = "Ajouter";
      insertButton.classList.add("insert-btn");
      insertButton.disable = false;
      insertButton.style.display = "block";
      cancelButton.textContent = "Cancel";
      cancelButton.classList.add("insert-btn");
      cancelButton.disable = false;
      cancelButton.style.display = "block";

      insertButton.onclick = () => {
        saveChanges(data, currentRowIndex);
        update(true, (err) => {
          insertMode = err ? true : false;
          if (!insertMode) renderRow(currentRowIndex);
        });
      };

      cancelButton.onclick = () => {
        if (rows.length > 1) {
          // si le seul élément est la nouvelle ligne alors il faut rester en insert mode
          insertMode = false;
          data.rows.splice(currentRowIndex, 1);
          rows.splice(currentRowIndex, 1);
          modifie.splice(currentRowIndex, 1);
          currentRowIndex =
            currentRowIndex == rows.length
              ? currentRowIndex - 1
              : currentRowIndex;
          renderRow(currentRowIndex);
        }
      };

      insertDiv.appendChild(insertButton);
      insertDiv.appendChild(cancelButton);
      container.appendChild(insertDiv);
    } else {
      container.appendChild(navDiv);
      container.appendChild(manageDiv);
    }

    setTitreClass();
  }

  renderRow(currentRowIndex);
}
