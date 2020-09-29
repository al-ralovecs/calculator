import { api } from '@/api';

export default {
    name: 'BaseCalculator',
    data() {
        return {
            result: 0,
            operation: null,
            savedOperation: null,
            previousKeyType: null,
            firstValue: null,
            itemsInMemory: 0,
        };
    },
    methods: {
        put(number) {
            if ('operator' === this.previousKeyType) {
                this.firstValue = this.result;
            }
            if (0 == number && 0 == this.result) {
                this.previousKeyType = 'number';
                return;
            }
            if (0 == this.result && 'decimal' === this.previousKeyType) {
                this.result += String(number);
                this.previousKeyType = 'number';
                return;
            }
            if (0 == this.result
                || 'operator' === this.previousKeyType
                || 'calculate' === this.previousKeyType
            ) {
                this.savedOperation = this.operation;
                this.operation = null;
                this.result = number;
            } else {
                this.result += String(number);
            }
            this.previousKeyType = 'number';
        },
        putDecimal() {
            if (! String(this.result).includes('.')) {
                this.result += '.';
            }
            this.previousKeyType = 'decimal';
        },
        clear() {
            this.result = 0;
            this.operation = null;
            this.savedOperation = null;
            this.previousKeyType = 'clear';
            this.firstValue = 'null';
        },
        setOperation(operation) {
            this.firstValue = this.result;
            this.previousKeyType = 'operator';
            if (this.operation === operation) {
                this.operation = null;
            }
            this.operation = operation;
        },
        calculate() {
            if (null === this.firstValue) {
                return;
            }
            if (null === this.savedOperation) {
                return;
            }

            let result = '';

            if (this.savedOperation === 'add') {
                result = Number(this.firstValue) + Number(this.result);
            } else if (this.savedOperation === 'subtract') {
                result = Number(this.firstValue) - Number(this.result);
            } else if (this.savedOperation === 'multiply') {
                result = Number(this.firstValue) * Number(this.result);
            } else if (this.savedOperation === 'divide') {
                result = Number(this.firstValue) / Number(this.result);
            }

            this.result = result;

            api.POST('/calc-result', {calc_result: result})
                .then((response => {
                    localStorage.setItem('token', response.token);
                    this.itemsInMemory = response.in_memory;
                }));

            this.previousKeyType = 'calculate';
        },
        inMemory(block) {
            return this.itemsInMemory >= block;
        },
        getFromMemory(block) {
            if (this.itemsInMemory < block) {
                return;
            }

            api.GET(`/calc-result/${block}`)
                .then((response) => {
                    this.result = response.calc_result;
                })
        }
    },
    mounted() {
        api.GET('/token')
            .then((response) => {
                localStorage.setItem('token', response.token);
                this.itemsInMemory = response.in_memory;
            });
    },
}
