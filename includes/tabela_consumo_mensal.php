<?php
/** 
 * Template de tabela de consumo por centro de custo
 * Espera a variável $resultado no escopo
 */

$totalCalc = number_format($resultado['total_calculado'], 2, ',', '.');
$totalArq  = number_format($resultado['total_arquivo'], 2, ',', '.');
$diferenca = number_format($resultado['diferenca'], 2, ',', '.');
$totalPercentual = number_format($resultado['total_percentual'], 2, ',', '.');
$status    = $resultado['total_inconsistente'] 
    ? '<span style="color:red">Inconsistente</span>' 
    : '<span style="color:green">OK</span>';

?>
<?php if (!empty($resultado['celulares_nao_encontrados'])): ?>
    <div>
        <h4 class="text-danger"><b>Linhas não encontradas no SCI:</b></h4>
        <?php foreach ($resultado['celulares_nao_encontrados'] as $numero): ?>
            <span class="badge bg-danger m-1"><?= $numero ?></span>
        <?php endforeach; ?>
    </div>
    <br><br>
<?php endif; ?>

<table class="table table-bordered table-hover" id="tableCusto">
    <thead>
        <tr>
            <th bgcolor="#d3d3d3">Rótulos de Linha</th>
            <th bgcolor="#d3d3d3">Soma de Valor do celular</th>
            <th bgcolor="#d3d3d3">Cód. UNN</th>
            <th bgcolor="#d3d3d3">Cód. CGA</th>
            <th bgcolor="#d3d3d3">Cód. CUS</th>
            <th bgcolor="#d3d3d3">Cód. SIN</th>
            <th bgcolor="#d3d3d3">Cód. ANA</th>
            <th bgcolor="#d3d3d3">DESCRIÇÃO</th>
            <th bgcolor="#d3d3d3">TOTAL</th>
            <th bgcolor="#d3d3d3">%</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resultado['centros_custo'] as $codigo => $dados): ?>
            <?php
                $valor = number_format($dados['valor'], 2, ',', '.');
                $porcentagem = number_format($dados['porcentagem'], 2, ',', '.');
                $centro = htmlspecialchars($dados['centro_custo'], ENT_QUOTES, 'UTF-8');
                list($unn, $cga, $cus, $sin, $ana) = explode('.', $codigo) + array(null, null, null, null, null);
            ?>
            <tr>
                <td><?= $codigo ?></td>
                <td><?= $valor ?></td>
                <td><?= $unn ?></td>
                <td><?= $cga ?></td>
                <td><?= $cus ?></td>
                <td><?= $sin ?></td>
                <td><?= $ana ?></td>
                <td><?= $centro ?></td>
                <td align="right"><?= $valor ?></td>
                <td align="right"><?= $porcentagem ?>%</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr bgcolor="#d3d3d3">
            <td colspan="8"><b>Total Calculado</b></td>
            <td align="right"><b><?= $totalCalc ?></b></td>
            <td></td>
        </tr>
        <tr bgcolor="#d3d3d3">
            <td colspan="8"><b>Total Arquivo</b></td>
            <td align="right"><b><?= $totalArq ?></b></td>
            <td></td>
        </tr>
        <tr bgcolor="#d3d3d3">
            <td colspan="8"><b>Diferença</b></td>
            <td align="right"><b><?= $diferenca ?></b></td>
            <td><?= $status ?></td>
        </tr>
    </tfoot>
</table>

<!-- Tabela para exportar para o Excel -->
<table class="table table-bordered table-hover" id="tableCustoExcel" style="display: none;">
    <thead>
        <tr>
            <th bgcolor="#d3d3d3">Cód. UNN</th>
            <th bgcolor="#d3d3d3">Cód. CGA</th>
            <th bgcolor="#d3d3d3">Cód. CUS</th>
            <th bgcolor="#d3d3d3">Cód. SIN</th>
            <th bgcolor="#d3d3d3">Cód. ANA</th>
            <th bgcolor="#d3d3d3">DESCRIÇÃO</th>
            <th bgcolor="#d3d3d3">TOTAL</th>
            <th bgcolor="#d3d3d3">%</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resultado['centros_custo'] as $codigo => $dados): ?>
            <?php
                $valor = number_format($dados['valor'], 2, ',', '.');
                $porcentagem = number_format($dados['porcentagem'], 2, ',', '.');
                $centro = htmlspecialchars($dados['centro_custo'], ENT_QUOTES, 'UTF-8');
                list($unn, $cga, $cus, $sin, $ana) = explode('.', $codigo) + array(null, null, null, null, null);
            ?>
            <tr>
                <td><?= $unn ?></td>
                <td><?= $cga ?></td>
                <td><?= $cus ?></td>
                <td><?= $sin ?></td>
                <td><?= $ana ?></td>
                <td><?= $centro ?></td>
                <td align="right"><?= $valor ?></td>
                <td align="right"><?= $porcentagem ?>%</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right"><b><?= $totalCalc ?></b></td>
            <td align="right"><b><?= $totalPercentual ?>%</b></td>
        </tr>
    </tfoot>
</table>
