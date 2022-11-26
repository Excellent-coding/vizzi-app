export default {
    computed: {
        isSelectedAll() {
          return this.selectedItems.length >= this.items.length;
        },
        isAnyItemSelected() {
            return (
                this.selectedItems.length > 0 &&
                this.selectedItems.length < this.items.length
            );
        }
    },
    methods: {
        selectAll(isToggle) {
            if (this.selectedItems.length >= this.items.length) {
              if (isToggle) this.selectedItems = [];
            } else {
              this.selectedItems = this.items.map(x => x.id);
            }
        },
        keymap(event) {
            switch (event.srcKey) {
              case "select":
                this.selectAll(false);
                break;
              case "undo":
                this.selectedItems = [];
                break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedItems.length > 0) {
              let itemsForToggle = this.items;
              var start = this.getIndex(itemId, itemsForToggle, "id");
              var end = this.getIndex(
                this.selectedItems[this.selectedItems.length - 1],
                itemsForToggle,
                "id"
              );
              itemsForToggle = itemsForToggle.slice(
                Math.min(start, end),
                Math.max(start, end) + 1
              );
              this.selectedItems.push(
                ...itemsForToggle.map(item => {
                  return item.id;
                })
              );
            } else {
              if (this.selectedItems.includes(itemId)) {
                this.selectedItems = this.selectedItems.filter(x => x !== itemId);
              } else this.selectedItems.push(itemId);
            }
        },
    },
}