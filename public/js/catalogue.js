(() => {
    const target = document.getElementById('catalogue');
    if (!window.Vue) {
        target.firstElementChild.textContent = 'Не удалось загрузить каталог.';
        return;
    }

    const fields = [
        { name: 'birth', label: 'По году рождения', placeholder: 'Введите год рождения' },
        { name: 'name', label: 'По ФИО', placeholder: 'Введите ФИО' },
        { name: 'description', label: 'По личной характеристике', placeholder: 'Введите личную характеристику' },
    ];
    const filterNames = [...fields.map(field => field.name), 'job_title_id'];
    const readFilters = (search = '') => {
        const params = new URLSearchParams(search);
        return Object.fromEntries(filterNames.map(name => [name, params.get(name) ?? '']));
    };

    async function fetchData(url, signal) {
        const response = await fetch(url, { signal });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message);
        }
        return result.data;
    }

    Vue.createApp({
        template: '#catalogue-template',
        data() {
            return {
                fields,
                filters: readFilters(location.search),
                employees: [],
                jobTitles: [],
                jobTitlesError: '',
                loading: true,
                error: '',
            };
        },
        created() {
            this.employeeRequest = null;
        },
        mounted() {
            this.loadEmployees(this.filters);
            fetchData('api/get_job_titles')
                .then(jobTitles => { this.jobTitles = jobTitles; })
                .catch(() => { this.jobTitlesError = 'Не удалось загрузить список должностей.'; });
            window.addEventListener('popstate', this.restoreFilters);
        },
        beforeUnmount() {
            window.removeEventListener('popstate', this.restoreFilters);
            this.employeeRequest?.abort();
        },
        methods: {
            restoreFilters() {
                this.filters = readFilters(location.search);
                this.loadEmployees(this.filters);
            },
            applyFilters() {
                const url = new URL(location.href);
                url.search = this.filterQuery(this.filters);
                if (url.href !== location.href) {
                    history.pushState(null, '', url);
                }
                this.loadEmployees(this.filters);
            },
            clearFilters() {
                this.filters = readFilters();
                this.applyFilters();
            },
            filterQuery(filters) {
                return new URLSearchParams(Object.entries(filters).filter(([, value]) => value !== ''));
            },
            async loadEmployees(filters) {
                this.employeeRequest?.abort();
                const request = new AbortController();
                this.employeeRequest = request;
                this.loading = true;
                this.error = '';

                try {
                    this.employees = await fetchData('api/read?' + this.filterQuery(filters), request.signal);
                } catch (error) {
                    if (!request.signal.aborted) {
                        this.error = 'Не удалось загрузить список сотрудников.';
                    }
                } finally {
                    if (!request.signal.aborted) {
                        this.loading = false;
                    }
                }
            },
        },
    }).mount(target);
})();
