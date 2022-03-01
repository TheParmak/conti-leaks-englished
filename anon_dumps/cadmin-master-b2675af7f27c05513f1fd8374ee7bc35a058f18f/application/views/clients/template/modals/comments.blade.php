<div class="dialog comments-modal">
    <h3><i class="fa fa-pencil" aria-hidden="true"></i> Comments for <small>@{{ currentClient.client }}</small></h3>
    <hr />

    <div style="width:100%;">
      <div style="width:100%; margin-bottom: 10px;">
          <span class="input-title">Your comment:</span> <input class="commentField" type="text" placeholder="Input comment..." title="Your comment" ng-model="currentClient.comment.comment_text" />
      </div>
      <br />
      <div style="width:100%; height: 50px;">
        <div class="pull-right">
          <button ng-click="saveComment()" style="z-index: 3; font-size: 15px; margin: 5px;">
            <i class="fa fa-save" aria-hidden="true"></i> Save
          </button>
          <button ng-click="closeCommentsDialog($event)" style="z-index: 3; font-size: 15px; margin: 5px;">
            <i class="fa fa-close" aria-hidden="true"></i> Cancel
          </button>
        </div>
    </div>
</div>
