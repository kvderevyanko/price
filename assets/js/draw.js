
let screenDrawLeft = $("#screenDrawLeft");
let screenDrawLeftResult = $("#screenDrawLeftResult");
let prevSize = $("#prevSize");
let drawActiveBlock = $(".drawActiveBlock");
let saveMainField = $("#saveMainField");
let modalSave = $("#modalSave");
let templateName = $("#templateName");
let saveTemplate = $("#saveTemplate");
let prevSmallImage = $("#prevSmallImage");
let modalLoadTemplate = $("#modalLoadTemplate");
let templatesList = $("#templatesList");
let onlyMy = $("#onlyMy");
let savePrice = $("#savePrice");
let logField = $("#logField");

let colorActive = "#000";
let colorClear = "#fff";

let w_px = 84;
let h_px = 48;

function generateDrawField(div){
    let content = '<table>';
    for (let h = 0; h < h_px; h++) {
        content+='<tr>';
        for (let w = 0; w < w_px; w++) {
            content+='<td></td>';
        }
        content+='</tr>';
    }
    div.html(content);
    //Получаем ширину всей колонки, делим на количество ячеек
    let fullWidth = div.width();
    fullWidth = fullWidth/w_px;

    div.find('td').css("width", fullWidth)
    div.find('td').css("height", fullWidth);
}

generateDrawField(screenDrawLeft);


function generateDrawResult(parentTd, resultTd) {
    let code = parentTd.html();
    resultTd.html(code);
    let fullWidth = prevSize.val();
    resultTd.find('td').css("width", fullWidth)
    resultTd.find('td').css("height", fullWidth);
}

prevSize.on('change', function() {
    generateDrawResult(screenDrawLeft, screenDrawLeftResult)
});

generateDrawResult(screenDrawLeft, screenDrawLeftResult);

screenDrawLeft.on('click', 'td', function (){
    drawPixel($(this));
    //generateDrawResult(screenDrawLeft, screenDrawLeftResult);
});

screenDrawLeft.on("mouseover", "td", function (event) {
    if(event.which === 1)
        drawPixel($(this));
})

function drawPixel(pixel) {
    if(!drawActiveBlock.hasClass("activeBlock"))
        return false;

    if($("#whiteTd").hasClass("activeBlock")) {
        pixel.css("background-color", colorClear);
        pixel.removeAttr("active");
    } else {
        pixel.css("background-color", colorActive);
        pixel.attr("active", 1);
    }
}

function generateHelperBlock(){
    drawActiveBlock.height(drawActiveBlock.width());
}

drawActiveBlock.on('click', function () {
    drawActiveBlock.removeClass("activeBlock");
    $(this).addClass("activeBlock");
})

generateHelperBlock();

saveMainField.on('click', function () {
    templateName.val('');
    modalSave.modal('show');
});

prevSmallImage.on('click', function(){
    generateDrawResult(screenDrawLeft, screenDrawLeftResult);
})

saveTemplate.on('click', function () {
    let code = {};
    $.each(screenDrawLeft.find('tr'), function (i, tr) {
        code[i] = {};
        $.each($(this).find('td'), function (t, td) {
            code[i][t] = $(this).attr('active')?$(this).attr('active'):0;
        })
    });

    $.post(urlSaveMainTemplate, {
        _csrf:$("[name='csrf-token']").attr('content'),
        name: templateName.val(),
        code:JSON.stringify(code),
    }, function(data) {
        if(data) {
            modalSave.modal('hide');
        }

    })
});

$("#loadImageTemplate").on('click', function (){
    modalLoadTemplate.modal('show');
    templatesList.html("Загрузка.....")
    $.get(urlLoadMainTemplate, function (data) {
        templatesList.html(data);
        showModalTemplateBlock();
    })
})

onlyMy.on('change', function(){
    let templatesAllUsers = $("#templatesAllUsers");
    if($(this).prop("checked")) {
        templatesAllUsers.hide();
        templatesAllUsers.find("input").prop("checked", false);
    } else {
        templatesAllUsers.show();
    }
    showModalTemplateBlock();
});

modalLoadTemplate.on('change', ".templateUsers", function(){
    showModalTemplateBlock();
});

function showModalTemplateBlock(){
    let users = {};
    if(onlyMy.prop("checked")) {
        users[onlyMy.data("user")] = 1;
    }

    $.each(modalLoadTemplate.find(".templateUsers"), function () {
        if($(this).prop("checked")) {
            users[$(this).data("user")] = 1;
        }
    })


    $.each(modalLoadTemplate.find(".modalTemplateBlock"), function () {
        if(users[$(this).data('user')]) {
            $(this).show();
        } else {
            $(this).hide();
        }
    })
}

modalLoadTemplate.on('click', ".deleteTemplate", function () {
    let id = $(this).data('id');
    let block = $(this).parents('.modalTemplateBlock');

    if(!id || !block)
        return false;

    if(confirm("Точно удалить?")) {
        $.post(urlDeleteMainTemplate, {
            id:id,
            _csrf:$("[name='csrf-token']").attr('content'),
        }, function (data) {
            if(data) {
                block.animate({"opacity":0}, 200, function (){
                    block.remove();
                });
            }
        })
        //block.fadeOut();
    }
    //block.fadeOut();
    return false;
});

//Рисуем сгенерированым кодом
function drawGenerateCode(code, drawField){
    try {

        code = JSON.parse(code);
        console.log(code);
        // код ...

    } catch (err) {
        code = false;
        console.log('Error')
        console.log(err)
    }

    if(code) {
        $.each(screenDrawLeft.find('tr'), function (i, tr) {
            $.each($(this).find('td'), function (t, td) {
                if(code[i] && code[i][t]) {
                    $(this).css("background-color", colorActive);
                    $(this).attr("active", 1);
                }
                //code[i][t] = $(this).attr('active')?$(this).attr('active'):0;
            })
        });

    }
}

if(codeGenerate) {
    drawGenerateCode(codeGenerate, screenDrawLeft);
}

savePrice.on('click', function(){
    logField.html('');

    let priceId = $("#priceId").val();
    let deviceId = $("#deviceId").val()
    if(!priceId || !deviceId) {
        logField.append('Не указан прайс и или устройство <br>');
        return false;
    }

    logField.append('Загрузка прайса ID: '+ priceId+' <br>');
    $.get(urlSavePrice, {
        id:priceId,
        contrast:$("#contrast").val(),
        bluePwm:$("#bluePwm").val(),
        blue:$("#blue").prop('checked')?1:0,
        deviceId:deviceId,
    }, function(data) {
        cl(data)
        logField.append('Данные загружены в прайс <br>');
        logField.append('Выполнение загрузки отображения <br>');
        $.get(urlShowPrice, {deviceId:deviceId}, function(data) {
            cl(data)
            logField.append('Данные отображены <br>');
        }).fail(function(data) {
            logField.append('Ошибка построения данных прайса ID: '+ priceId+' <br>');
            cl(data)
        })
    }).fail(function(data) {
        logField.append('Ошибка отправки прайса ID: '+ priceId+' <br>');
        cl(data)
    })
})

$("#syncBase").on('click', function(){
    logField.html('');
    logField.append('Синхронизация баз <br>');
    logField.append('Получение данных <br>');

    $.post(urlSyncBase, {
        _csrf:$("[name='csrf-token']").attr('content'),
    }, function (data) {
        logField.append('Синхронизировано '+data+' <br>');
    }).fail(function(data) {
        logField.append('Ошибка синхронизации <br>');
        cl(data)
    })

});