<?php

/** @var array $data */
headerAdmin($data);
?>
<style>
  .app-content {
    position: relative;
    min-height: 100vh;
  }

  .app-content::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background-image: url('<?= media(); ?>/images/DMLogo.png');
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 40%;
    opacity: 0.15;
    z-index: 99;
    pointer-events: none;
  }
</style>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-dashboard"></i> Distribuidora Morera</h1>

    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>dashboard">Dashboard</a></li>
    </ul>
  </div>
  <!-- Area de widgets -->
  <div class="row">
    <?php if (!empty($_SESSION['permisos'][1]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/usuarios" class="linkw">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info">
              <h4>Usuarios</h4>
              <p><b><?= $data['usuarios'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
    <?php if (!empty($_SESSION['permisos'][4]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/clientes" class="linkw">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-user-o fa-3x"></i>
            <div class="info">
              <h4>Clientes</h4>
              <p><b><?= $data['clientes'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
    <?php if (!empty($_SESSION['permisos'][5]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/productos" class="linkw">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-archive fa-3x"></i>
            <div class="info">
              <h4>Productos</h4>
              <p><b><?= $data['productos'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
    <?php if (!empty($_SESSION['permisos'][8]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/movimientos" class="linkw">
          <div class="widget-small danger coloured-icon"><i class="icon fa fa-shopping-cart fa-3x"></i>
            <div class="info">
              <h4>Movimientos pendientes</h4>
              <p><b><?= $data['movimientos'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
  </div>
  <!-- Tabla de Movimientos Recientes -->
  <div class="row">
    <?php if (!empty($_SESSION['permisos'][8]['r'])) { ?>
      <div class="col-md-6">
        <div class="tile">
          <h3 class="tile-title">Movimientos Recientes</h3>
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Usuario</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (count($data['ultimosMovimientos']) > 0) {
                foreach ($data['ultimosMovimientos'] as $movimiento) {
              ?>
                  <tr>
                    <td><?= $movimiento['id_movimiento'] ?></td>
                    <td><?= $movimiento['nombre_razon_social'] ?></td>
                    <td><?= $movimiento['estado_badge'] ?></td>
                    <td><?= $movimiento['nombre_completo'] ?></td>
                    <td class="text-center">
                      <button type="button" 
                      class="btn btn-info btn-sm btnViewMovimiento"
                      cl=<?= $movimiento['id_movimiento'] ?>"
                      title="Ver"><i class="fas fa-eye"></i>
                      </button>
                     </td>
                  </tr>
              <?php }
              } else{
              ?>
              <tr>
                <td colspan="5" class= "text-center">No hay movimientos recientes.>/td>

              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } ?>
</div>
<!-- Área de Gráficos -->
    <div class="row">
    <!-- Gráfico 1 (Izquierda) -->
    <div class="col-md-6">
      <div class="tile">
        <div class="conatainer-title">
            <h3 class="tile-title">Análisis de movimientos por día</h3>
        </div>
        <div id="movimientosDia"></div>
      </div>
    </div>
    <!-- Gráfico 2 (Derecha) -->
    <div class="col-md-6">
      <div class="tile">
        <div class="conatainer-title">
           <h3 class="tile-title">Análisis de movimientos por mes</h3>
        </div>
        <div id="movimientosMesAnio"></div>
      </div>
    </div>
  </div>
  
</main>
<?php 
getModal('modalMovimientos', $data);
footerAdmin($data); 
?>
<script src="<?= media(); ?>/js/functions_movimientos.js"></script>
<script>
 
  Highcharts.chart('movimientosMesAnio', {
    chart: {
      type: 'pie'
    },

    title: {
      text: 'Movimientos, <?= $data['tipoMovMes']['mes'].'_'.$data['tipoMovMes']['anio'] ?>'
    },

    palette: {
      light: {
        colors: ['#e50101', '#1250e1', '#0b8c43', '#034902', '#034902']
      },
      dark: {
        colors: ['#e50101', '#1250e1', '#0b8c43', '#034902', '#034902']
      }
    },

    tooltip: {
      valueSuffix: '%'
    },

    series: [{
      name: 'Representa',
      // We can show multiple data labels per point
      dataLabels: [{
          format: '{point.name}',
          connectorColor: 'var(--highcharts-neutral-color-80, #333)'
        },
        {
          format: '{point.percentage:.0f}%',
          backgroundColor: 'contrast',
          distance: -30, // Placing the label inside
          style: {
            fontSize: '0.9em',
            textOutline: 'none'
          }
        }
      ],
      data: [
       <?php 
        if (!empty($data['tipoMovMes']['datos'])) {
            foreach ($data['tipoMovMes']['datos'] as $mov) {
                echo "{ name: '" . $mov['tipo_movimiento'] . "', y: " . intval($mov['total_movimientos']) . " },";
            }
        }
        ?>
      ]
    }]
  });
</script>
<!--Graf lineas-->
<?php
$fechasDia   = [];
$recibidosDia = [];
$entregasDia = [];

if (!empty($data['versusMov']['datos'])) {
    foreach ($data['versusMov']['datos'] as $row) {
        $fechasDia[]    = $row['fecha_formato'];
        $recibidosDia[] = intval($row['total_recibidos']);
        $entregasDia[]  = intval($row['total_entregas']);
    }
}
?>
<script>
  Highcharts.chart('movimientosDia', {
    chart: {
      plotBorderColor: 'var(--highcharts-neutral-color-10, #e6e6e6)',
      plotBorderWidth: 1,
      plotBorderRadius: 5
    },

    title: {
      text: 'Salidas Entregadas por Día (<?= $data['versusMov']['mes'] . '/' . $data['versusMov']['anio'] ?>)',
      align: 'left'
    },

    subtitle: {
      text: 'Comparativa diaria de salidas registradas frente a entregas completadas',
      align: 'left'
    },

    yAxis: {
      allowDecimals: false,
      title: {
        text: 'Cantidad de Movimientos'
      }
    },

    xAxis: {
      categories: <?= json_encode($fechasDia) ?>,
      crosshair: true,
      labels: {
        formatter: function () {
          return String(this.value).split('/')[0];
        }
      },
      lineWidth: 0,
      tickLength: 6,
      tickColor: 'var(--highcharts-neutral-color-10, #e6e6e6)'
    },

    legend: {
      enabled: true
    },

    tooltip: {
      shared: true,
      headerFormat: `<table>
            <caption>Fecha: <strong>{point.key}</strong></caption>`,
      pointFormat: `
        <tr>
          <th>
            <svg width="20" height="10">
              <path d="M 5 5 L 13 5" stroke="{series.color}" stroke-width="5"
                stroke-linecap="round" />
            </svg>
            {series.name}
          </th>
          <td><strong>{point.y}</strong></td>
        </tr>
        `,
      footerFormat: '</table>',
      useHTML: true
    },

    plotOptions: {
      series: {
        marker: {
          enabled: false
        }
      }
    },

    series: [{
        name: 'Entregas Exitosas',
        color: 'light-dark(#0097FF, #27A6FE)',
        data: <?= json_encode($entregasDia) ?>
      },
      {
        name: 'Pedidos Recibidos',
        color: '#8791BA',
        dashStyle: 'ShortDot',
        data: <?= json_encode($recibidosDia) ?>
      }
    ]
  });
</script>