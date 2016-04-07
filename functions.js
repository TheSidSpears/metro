function only_num(input) {
    input.value = input.value.replace(/[\D,]/g, '');
};