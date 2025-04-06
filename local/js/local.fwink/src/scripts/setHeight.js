
function setHeightBottomBlock() {

    let blocks = document.querySelectorAll('.bottom-block');

    function heightPage(n) {
        return n.clientHeight;
    }
    function getBlock(arr) {
        let maxHeight = 0;
        arr.forEach(item => {
            if ( heightPage(item) > maxHeight) {
                maxHeight = heightPage(item);
            }
        })
        arr.forEach(item => item.style.height = `${maxHeight}px`);
    }
    return getBlock(blocks);
}
export default setHeightBottomBlock;
