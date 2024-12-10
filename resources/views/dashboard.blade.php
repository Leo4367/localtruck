<!DOCTYPE html>
<html>
<body>
<div class="chart-pickup">
    <canvas id="pickup"></canvas>
</div>

<div class="chart-delivery">
    <canvas id="delivery"></canvas>
</div>

<script src="https://cdn.staticfile.net/Chart.js/3.9.1/chart.js"></script>
<script type="text/javascript">
    const pickups = @json($pickups);
    const deliveries = @json($deliveries);

    const Pickup = document.getElementById('pickup');
    const Delivery = document.getElementById('delivery');
    const pickupData = {
        labels: pickups.labels, // 使用动态传递的 labels
        datasets: [{
            label: 'Pickup',
            data: pickups.dataset, // 使用动态传递的数据
            fill: false,
            // borderColor: 'rgb(255,0,0)', // 设置线的颜色
            borderColor: '#f2ae78', // 设置线的颜色
            backgroundColor: '#ff0000',
            titleColor: '#ffffff',
            tension: 0.1
        }]
    };

    const DeliveryData = {
        labels: deliveries.labels, // 使用动态传递的 labels
        datasets: [{
            label: 'Delivery',
            data: deliveries.dataset, // 使用动态传递的数据
            fill: false,
            //borderColor: 'rgb(255,0,0)', // 设置线的颜色
            borderColor: '#f2ae78', // 设置线的颜色
            backgroundColor: '#ff0000',
            titleColor: '#ffffff',
            tension: 0.1
        }]
    };


    const configPickup = {
        type: 'line',
        data: pickupData,
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        color: '#ffffff' // 设置 Y 轴标题颜色
                    },
                    ticks: {
                        color: '#ffffff' // 设置 X 轴刻度字体颜色
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'count',
                        color: '#ffffff' // 设置 Y 轴标题颜色
                    },
                    ticks: {
                        stepSize: 1,// 设置 Y 轴的步长为 1
                        color: '#ffffff',
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff' // 设置图例的字体颜色
                    }
                },
                tooltip: {
                    bodyColor: '#ffffff', // 设置提示框内容字体颜色
                    titleColor: '#ffffff' // 设置提示框标题字体颜色
                }
            }
        },
        plugins: [{
            id: 'customCanvasBackgroundColor',
            beforeDraw: (chart) => {
                const ctx = chart.canvas.getContext('2d');
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = '#5f5ba7'; // 图表区域背景颜色
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        }]
    };

    const configDelivery = {
        type: 'line',
        data: DeliveryData,
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        color: '#ffffff' // 设置 Y 轴标题颜色
                    },
                    ticks: {
                        color: '#ffffff' // 设置 X 轴刻度字体颜色
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count',
                        color: '#ffffff' // 设置 Y 轴标题颜色
                    },
                    ticks: {
                        stepSize: 1,// 设置 Y 轴的步长为 1
                        color: '#ffffff',
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff' // 设置图例的字体颜色
                    }
                },
                tooltip: {
                    bodyColor: '#ffffff', // 设置提示框内容字体颜色
                    titleColor: '#ffffff' // 设置提示框标题字体颜色
                },
            }
        },
        plugins: [{
            id: 'customCanvasBackgroundColor',
            beforeDraw: (chart) => {
                const ctx = chart.canvas.getContext('2d');
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = '#5f5ba7'; // 图表区域背景颜色
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        }]

    };
    const pickupChart = new Chart(Pickup, configPickup);
    const deliveryChart = new Chart(Delivery, configDelivery);
</script>
</body>
</html>
<style>
    /* 设置一个容器，使图表居中并限制大小 */
    .chart-pickup {
        width: 45%; /* 让图表占页面宽度的80% */
        max-width: 700px; /* 最大宽度为800px */
        margin-left: 25px;
        padding: 20px; /* 给容器增加一些内边距 */
        float: left;
        background-color: #5f5ba7;
        border-radius: 25px;
    }

    /* 可选：为图表的 canvas 元素设置自适应的宽高 */
    #pickup {
        width: 100% !important; /* 图表宽度设置为容器的100% */
        height: 400px; /* 固定高度 */
    }

    /* 设置一个容器，使图表居中并限制大小 */
    .chart-delivery {
        width: 45%; /* 让图表占页面宽度的80% */
        max-width: 700px; /* 最大宽度为800px */
        margin-right: 25px;
        padding: 20px; /* 给容器增加一些内边距 */
        float: right;
        background-color: #5f5ba7;
        border-radius: 25px;
    }

    /* 可选：为图表的 canvas 元素设置自适应的宽高 */
    #delivery {
        width: 100% !important; /* 图表宽度设置为容器的100% */
        height: 400px; /* 固定高度 */
    }
</style>
