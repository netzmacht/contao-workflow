
function workflowConditionalTransitionsUpdateEditButton(button)
{
    if (button) {
        button       = $(button);
        var image    = button.getElement('img');
        var select   = button.getParent('tr').getElement('select');
        var moduleID = select.value;
        var label    = select.options[select.selectedIndex].innerHTML;

        if (/^\d+$/.exec(moduleID)) {
            image.src          = image.src.replace('edit_.svg', 'edit.svg');
            button.moduleID    = moduleID;
            button.moduleTitle = label;
            button.setStyle('cursor', '');
        } else {
            image.src          = image.src.replace('edit.svg', 'edit_.svg');
            button.moduleID    = null;
            button.moduleTitle = null;
            button.setStyle('cursor', 'default');
        }
    }
}

function workflowConditionalTransitionsButtonClick()
{
    if (this.moduleID) {
        var rt   = /[&\?](rt=[^&]+)/.exec(document.location.search);
        var href = 'contao?do=workflows&table=tl_workflow_transition&act=edit&id=' + this.moduleID
            + '&popup=1&nb=1'
            + (rt ? '&' + rt[1] : '');

        Backend.openModalIframe(
            {
                'title': this.moduleTitle,
                'url': href
            }
        );
    }
}

$(window).addEvent('domready', function () {
    MultiColumnWizard.addOperationUpdateCallback('new', function (el, row) {
        console.log(el,  row);
        var button = $(row).getElement('a.edit_transition');
        workflowConditionalTransitionsUpdateEditButton(button);
        button.addEvent('click', workflowConditionalTransitionsButtonClick);
        $(row).getElement('select').addEvent('change', function () {
            workflowConditionalTransitionsUpdateEditButton($(this).getParent('tr').getElement('a.edit_transition'));
        });
    });

    $$('#ctrl_conditionalTransitions select').addEvent('change', function () {
        workflowConditionalTransitionsUpdateEditButton($(this).getParent('tr').getElement('a.edit_transition'));
    });
    $$('#ctrl_conditionalTransitions a.edit_transition').each(function (button) {
        workflowConditionalTransitionsUpdateEditButton(button);
        button.addEvent('click', workflowConditionalTransitionsButtonClick);
    });
});
