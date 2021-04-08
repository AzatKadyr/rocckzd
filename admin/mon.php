 <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Мониторинг сервера</h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
              <button type="button" class="btn btn-sm btn-outline-secondary"></button>
              <button type="button" class="btn btn-sm btn-outline-secondary">Экспорт</button>
            </div>
          </div>
        </div>

        <div class="table-responsive">
            <p id="countmon">Количество аудитов в очереди:</p>
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>Ресторан</th>
                <th>Аудитор</th>
                <th>Результат</th>
                <th>Добавлено в очередь</th>
                <th>Статус выполнения</th>
                <th>Время выполнения</th>
              </tr>
            </thead>
            <tbody id="monresult"></tbody>
          </table>
        </div>