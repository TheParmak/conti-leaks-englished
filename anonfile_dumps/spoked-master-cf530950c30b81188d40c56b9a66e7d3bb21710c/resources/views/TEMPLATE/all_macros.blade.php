<!-- Macroses list -->
<div class="modal fade" id="all-macroses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">All Macroses</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Macros</th>
                      <th>Description</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">1</th>
                      <td>Mark</td>
                      <td>Otto</td>
                    </tr>
                    <tr>
                      <th scope="row">3</th>
                      <td>Jacob</td>
                      <td>Thornton</td>
                    </tr>
                    <tr>
                      <th scope="row">4</th>
                      <td>Jacob</td>
                      <td>Thornton</td>
                    </tr>
                    <tr>
                      <th scope="row">5</th>
                      <td>Jacob</td>
                      <td>Thornton</td>
                    </tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Number Variables Macros -->
<div class="modal fade" id="create-number-macros-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Number Variable</h4>
            </div>
            <div class="modal-body">
                <div class="input-group" id="name-check-num-group" style="margin-bottom: 10px;">
                  <span class="input-group-addon" id="basic-addon1">Name</span>
                  <input type="text" class="form-control" placeholder="MyVar" aria-describedby="basic-addon1" ng-model="numberName" ng-init="numberName = null">
                </div>
                <div class="input-group" id="len-check-num-group">
                  <span class="input-group-addon" id="basic-addon1">Length</span>
                  <input type="text" class="form-control" placeholder="0" aria-describedby="basic-addon1" ng-model="numberLength" ng-init="numberLength = null">
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" ng-model="isSaveNum" ng-init="isSaveNum = false;">Save?</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" ng-click="createNumber(numberName, numberLength, isSaveNum); numberLength = null; numberName = null; isSaveNum = false">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create String Variables Macros -->
<div class="modal fade" id="create-string-macros-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Create String Variable</h4>
            </div>
            <div class="modal-body">
                <div class="input-group" style="margin-bottom: 10px;">
                  <span class="input-group-addon" id="basic-addon1">Name</span>
                  <input type="text" class="form-control" placeholder="MyVar" aria-describedby="basic-addon1" ng-model="numberName" ng-init="numberName = null">
                </div>
                <div class="input-group">
                  <span class="input-group-addon" id="basic-addon1">Length</span>
                  <input type="text" class="form-control" placeholder="0" aria-describedby="basic-addon1" ng-model="numberLength" ng-init="numberLength = null">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" ng-click="createString(numberName, numberLength); numberLength = null; numberName = null;">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Insert Number Macros -->
<div class="modal fade" id="insert-number-macros-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Insert Number Macros</h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                  <span class="input-group-addon" id="basic-addon1">Length</span>
                  <input type="text" class="form-control" placeholder="0" aria-describedby="basic-addon1" ng-model="numberLength" ng-init="numberLength = null">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" ng-click="insertNumber(numberLength); numberLength = null;">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Insert Number Macros -->
<div class="modal fade" id="insert-string-macros-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Insert String Macros</h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                  <span class="input-group-addon" id="basic-addon1">Length</span>
                  <input type="text" class="form-control" placeholder="0" aria-describedby="basic-addon1" ng-model="numberLength" ng-init="numberLength = null">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" ng-click="insertString(numberLength); numberLength = null;">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Insert Variables Macros -->
<div class="modal fade" id="insert-variable-macros-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Insert Variable Macros</h4>
            </div>
            <div class="modal-body">
                 <div class="form-group">
                    <label for="sel1">Select variable:</label>
                    <select class="form-control" ng-model="selectVar" placeholder="Not selected" ng-init="selectVar = null;">
                      <option ng-repeat="cur in currentVaraibles" value="@{{ cur['name'] }}">@{{ cur["name"] }}</option>
                    </select>
                  </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" ng-click="insertVariable(selectVar); selectVar = null;">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>