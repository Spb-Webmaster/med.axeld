/**
 * MoonShine v4 — Tab Persistence
 *
 * Сохраняет активный таб при отправке формы и восстанавливает его после перезагрузки страницы.
 * Использует localStorage с ключом по URL-пути, поэтому работает независимо для каждой страницы.
 *
 * Подключение (в активном Layout, метод assets()):
 *
 *   use MoonShine\AssetManager\Js;
 *
 *   protected function assets(): array
 *   {
 *       return [
 *           ...parent::assets(),
 *           new Js('/js/admin/tab-persist.js'),
 *       ];
 *   }
 *
 * Активный layout определяется в config/moonshine.php → 'layout'.
 * Файл поместить в public/js/admin/tab-persist.js.
 */
(function () {

    function getTabKey() {
        return 'activeTab:' + location.pathname;
    }

    function tryClickTab(savedId, attempt) {
        attempt = attempt || 0;
        if (attempt > 20) return;

        var buttons = document.querySelectorAll('button.tabs-button');
        if (buttons.length === 0) {
            setTimeout(function () { tryClickTab(savedId, attempt + 1); }, 100);
            return;
        }

        var found = false;
        buttons.forEach(function (btn) {
            var attr = btn.getAttribute('@click.prevent') || '';
            var match = attr.match(/setActiveTab\(`(\d+)`\)/);
            if (match && match[1] === savedId) {
                btn.click();
                found = true;
            }
        });

        if (!found) {
            localStorage.removeItem(getTabKey());
        }
    }

    // Восстановить активный таб
    var savedId = localStorage.getItem(getTabKey());
    if (savedId) {
        tryClickTab(savedId);
    }

    // Сохранить активный таб перед отправкой формы
    document.addEventListener('submit', function () {
        var activeBtn = document.querySelector('button.tabs-button._is-active');
        if (activeBtn) {
            var attr = activeBtn.getAttribute('@click.prevent') || '';
            var match = attr.match(/setActiveTab\(`(\d+)`\)/);
            if (match) {
                localStorage.setItem(getTabKey(), match[1]);
            }
        }
    });

}());
