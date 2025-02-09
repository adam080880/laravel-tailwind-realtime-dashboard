@extends('template.dashboard')

@section('dashboard_content')
  <div id="loading-indicator" class="min-h-[500px] min-w-full flex justify-center items-center">
    <h2 class="text-center">Mohon Tunggu...</h2>
  </div>
  <div class="hidden w-full" id="main-content">
    <div class="mb-2 max-w-[300px]">
      <label for="branch" class="block mb-2 text-sm font-medium text-gray-900">Cabang</label>
      <select id="branch" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
      </select>
    </div>

    <div id="chart-data" class="grid grid-cols-1 gap-4 w-full">
    </div>

    <hr class="my-3" />

    <div id="table-data" class="grid grid-cols-1 gap-4 w-full">

    </div>
  </div>

  <script>
    let selectedBranch;
    let labelsByBranch;
    let dataByLabel = null;
    let historyBySource = null;
    let intervalFetchingData = null;
    let datatableBySource = null;
    let chartByLabel = null;

    const getAllBranches = async () => {
      const branchesAndLabels = await ((await fetch("{{route('stream_data.branches_label')}}")).json());
      const branches = Object.keys(branchesAndLabels);

      labelsByBranch = branchesAndLabels;

      document.getElementById('branch').innerHTML = '<option selected>Pilih cabang</option>';
      document.getElementById('branch').innerHTML = document.getElementById('branch').innerHTML + `${branches.map((branch) => `<option value='${branch}'>${branch}</option>`).join('')}`;

      return branches;
    };

    const loadData = async () => {
      const newData = await ((await fetch(
        "{{route('stream_data.by_labels')}}",
        {
          method: 'POST',
          body: JSON.stringify({labels: labelsByBranch[selectedBranch]}),
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
        }
      )).json());
      const histories = await ((await fetch(
        "{{route('stream_data.history_by_labels')}}",
        {
          method: 'POST',
          body: JSON.stringify({labels: labelsByBranch[selectedBranch]}),
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
        }
      )).json());

      const newHistoriesBySource = histories.reduce((prev, curr) => {
        return {
          ...prev,
          [curr.pointName.split('.')[2]]: {
            ...prev[curr.pointName.split('.')[2]],
            [curr.pointName]: [curr.pointValue],
          },
        };
      }, {});

      if (!historyBySource) {
        historyBySource = newHistoriesBySource;
      } else {
        Object.keys(newHistoriesBySource).forEach((source) => {
          Object.keys(newHistoriesBySource[source]).forEach((label) => {
            historyBySource[source][label].unshift(newHistoriesBySource[source][label][0]);
            historyBySource[source][label] = historyBySource[source][label].slice(0, 100);
          });
        });
      }

      const newSnapshot = newData.reduce((prev, curr) => {
        return {
          ...prev,
          [curr.pointName]: [curr],
        };
      }, {});

      newData.forEach((d) => {
        document.getElementById(`${d.pointName}-condition-label`).classList.remove('text-red-500', 'text-green-500');
        document.getElementById(`${d.pointName}-condition-label`).innerHTML = d.pointQuality;

        if (d.pointQuality === 'Good') {
          document.getElementById(`${d.pointName}-condition-label`).classList.add('text-green-500');
        } else {
          document.getElementById(`${d.pointName}-condition-label`).classList.add('text-red-500');
        }
      });

      Object.keys(historyBySource).forEach((source) => {
        document.getElementById(`${source}-table`).innerHTML = `
          <tr>
            ${Object.keys(historyBySource[source]).sort().map((pointName) => {
              return `<td class="px-6 py-3" style="white-space: nowrap;">
                ${historyBySource[source][pointName][0]}
              </td>`;
            }).join('')}
            <td class="px-6 py-3">
              ${dayjs(historyBySource[source][Object.keys(historyBySource[source])[0]].slice(-1)[0].pointTimestamp).format('YYYY-MM-DD HH:mm:ss')}
            </td>
          </tr>
        ` + document.getElementById(`${source}-table`).innerHTML;
      });

      // append snapshot to dataByLabel
      if (!dataByLabel) {
        dataByLabel = newSnapshot;
      } else {
        Object.keys(newSnapshot).forEach((label) => {
          dataByLabel[label].push(newSnapshot[label][0]);
          dataByLabel[label].reverse().slice(0, 5).reverse();
        });
      }

      if (!chartByLabel) {
        chartByLabel = {};
        Object.keys(dataByLabel).map((label) => {
          chartByLabel[label] = new Chart(document.getElementById(`${label}-canvas`), {
            type: 'line',
            data: {
              labels: [dayjs().format('HH:mm:ss')],
              datasets: [{
                label: '#',
                data: dataByLabel[label].slice(-1).map((d) => d.pointValue),
                borderWidth: 1
              }]
            },
          });
        });
      } else {
        Object.keys(dataByLabel).forEach((label) => {
          chartByLabel[label].data.labels.push(dayjs().format('HH:mm:ss'));
          chartByLabel[label].data.labels = chartByLabel[label].data.labels.reverse().slice(0, 5).reverse();
          chartByLabel[label].data.datasets.forEach((d) => {
            d.data.push(dataByLabel[label].slice(-1)[0].pointValue);
            d.data = d.data.reverse().slice(0, 5).reverse();
          });
          chartByLabel[label].update();
        });
      }
    };

    const startFetchingData = (currentBranch) => {
      if (intervalFetchingData) {
        clearInterval(intervalFetchingData);
      }

      if (!currentBranch) {
        return;
      }

      intervalFetchingData = setInterval(loadData, 5000);
    };

    window.onload = async function() {
      await getAllBranches();
      document.getElementById('loading-indicator').classList.add('hidden');
      document.getElementById('main-content').classList.remove('hidden');

      document.getElementById('branch').addEventListener('change', function(e) {
        selectedBranch = e.target.value;
        dataByLabel = null;
        chartByLabel = null;

        historyBySource = labelsByBranch[selectedBranch].reduce((prev, curr) => {
          return {
            ...prev,
            [curr.split('.')[2]]: {
              ...prev[curr.split('.')[2]],
              [curr]: []
            },
          };
        }, {});

        const sources = Object.keys(historyBySource).sort();

        document.getElementById('chart-data').innerHTML = sources.map(source => {
          let a = `<div class='px-[20px] py-[16px] bg-primary mb-[12px] text-white'>
            <span class="font-bold text-[20px]">${source}</span>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 w-full mb-2 mt-2">`;

          labelsByBranch[selectedBranch].map(label => {
            const labelShow = label.split('.');
            labelShow.shift();

            if (labelShow[1] === source) {
              a = a + `
                <div class="block w-full p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-[12px] font-bold text-gray-700 text-wrap">${labelShow[2].replace(/\_/g, ' ')}</span>
                    <span id="${label}-condition-label" class="font-bold text-[12px]"></span>
                  </div>
                  <canvas id="${label}-canvas" class="w-full h-[320px]"></canvas>
                </div>
              `;
            }
          }).join('');

          a = a + `</div></div>`;

          return a;
        }).join('');

        document.getElementById('table-data').innerHTML = Object.keys(historyBySource).map((source) => {
          return `
            <div class="block w-full p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
              <div class="flex justify-between items-center mb-2">
                <span class="text-[16px] font-bold text-gray-700 text-wrap">${source}</span>
              </div>
              <div class="relative overflow-x-auto w-full h-[450px] overflow-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 bg-background">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                      <tr>
                        ${Object.keys(historyBySource[source]).sort().map((pointName) => {
                          return `<th scope="col" class="px-6 py-3" style="white-space: nowrap;">
                            ${pointName.split(['.'])[3].replace(/\_/g, ' ')}
                          </th>`;
                        }).join('')}
                        <th scope="col" class="px-6 py-3">
                            pointTimestamp
                        </th>
                      </tr>
                  </thead>
                  <tbody id="${source}-table">
                  </tbody>
              </table>
              </div>
            </div>
          `;
        }).join('');

        setTimeout(() => {
          loadData();
          startFetchingData(e.target.value);
        }, 500);
      });
    };
  </script>
@endsection
