const elements = {
    btnUpload: document.getElementById("upload"),
    fileInput: document.getElementById("arquivo"),
    operadora: document.getElementById("operadora"),
    centroCusto: document.getElementById("centro_custo_padrao"),
    result: document.getElementById("result"),
    btnExportar: document.getElementById("export_excel"),
    btnSalvarParametro: document.getElementById("salvar_parametro")
}


function validateFile() {
  const file = elements.fileInput.files[0];

  if (!file) {
    elements.fileInput.setCustomValidity("Por favor, selecione um arquivo.");
  } else if (!file.name.toLowerCase().endsWith(".zip")) {
    elements.fileInput.setCustomValidity("Por favor, selecione um arquivo ZIP.");
  } else {
    elements.fileInput.setCustomValidity("");
  }

  elements.fileInput.reportValidity();
  return elements.fileInput.validity.valid;
}

async function handleUpload() {
  try {
    if (!validateFile()) {
      return;
    }

    elements.btnUpload.disabled = true;

    const formData = new FormData();
    formData.append("arquivo", elements.fileInput.files[0]);
    formData.append("operadora", elements.operadora.value);
    formData.append("centro_custo", elements.centroCusto.value);

    const response = await fetch("includes/importar_consumo_mensal.php", {
      method: "POST",
      body: formData,
      headers: {
        "Accept": "application/json;charset=utf-8"
      }
    });

    const json   = await response.json();

    if (response.status === 200) {
      elements.result.innerHTML = json.dados.html;
    } else {
      throw new Error(json.dados.erro);
    }
  } catch (error) {
    $('#msg').html("Erro ao fazer upload do arquivo: " + error.message);
    $('#myModal_alert').modal('show');
  } finally {
    elements.btnUpload.disabled = false;
  }
}
const handleExport = () => {
  const table = elements.result.querySelector('#tableCustoExcel');
  if (!table) return;

  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet('Relatório de Consumo Mensal');

  const rows = table.querySelectorAll('tr');
  rows.forEach((row, rowIndex) => {
    const rowData = Array.from(row.querySelectorAll('th, td')).map(cell => cell.textContent);
    const excelRow = worksheet.addRow(rowData);

    // Aplicar formatação
    excelRow.eachCell((cell, colNumber) => {
      // Negrito na primeira linha
      if (rowIndex === 0) {
        cell.font = { bold: true };
      }

      // Bordas pretas em todas as células
      cell.border = {
        top: { style: 'thin', color: { argb: 'FF000000' } },
        left: { style: 'thin', color: { argb: 'FF000000' } },
        bottom: { style: 'thin', color: { argb: 'FF000000' } },
        right: { style: 'thin', color: { argb: 'FF000000' } },
      };
    });
  });

  workbook.xlsx.writeBuffer().then(data => {
    const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'relatorio_consumo_mensal.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  });
};

const handleSalvarParametro = async () => {
  elements.btnSalvarParametro.disabled = true;
  const centroCusto = elements.centroCusto.value;

  const formData = new FormData();
  formData.append("centro_custo", centroCusto);

  try {
    const response = await fetch("includes/salvar_parametro.php", {
      method: "POST",
      body: formData,
      headers: {
        "Accept": "application/json;charset=utf-8"
      }
    });

    const json   = await response.json();
    if (response.status === 200) {
      $('#msg_success').html("Salvo com sucesso");
      $('#myModal_alert_sucess').modal('show');
    } else {
      throw new Error(json.dados.erro);
    }
  } catch (error) {
    $('#msg').html("Erro ao salvar: " + error.message);
    $('#myModal_alert').modal('show');
  } finally {
    elements.btnSalvarParametro.disabled = false;
  }
}

document.addEventListener("DOMContentLoaded", function() {
  elements.btnUpload.addEventListener("click", handleUpload);
  elements.btnExportar.addEventListener("click", handleExport);
  elements.btnSalvarParametro.addEventListener("click", handleSalvarParametro);
});
