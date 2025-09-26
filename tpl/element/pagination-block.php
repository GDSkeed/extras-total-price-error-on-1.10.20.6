<?php if ($pages): ?>
    <div class="section">
        <div class="container">

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">

                    <?php if ($pages->current > 1 && $pages->total > 1): ?>
                        <li class="page-item"><a class="page-link" data-page="<?= $pages->current - 1 ?>" href="<?= hfyGetLinkWithPage($pages, $pages->current - 1) ?>"><?= __('Previous', 'hostifybooking') ?></a></li>
                        <li class="page-item"><a class="page-link" data-page="1" href="<?= hfyGetLinkWithPage($pages, 1) ?>">1</a></li>
                    <?php else: ?>
                        <li class="page-item disabled"><a class="page-link" href="">1</a></li>
                    <?php endif; ?>

                    <?php if ($pages->current > 3): ?>
                        <li class="page-item disabled">...</li>
                    <?php endif; ?>

                    <?php
                    $page_from = $pages->current > 3 ? $pages->current - 1 : 2;
                    $page_to = $pages->current + 1 < $pages->total ? $pages->current + 1 : $pages->total;
                    for ($i = $page_from; $i <= $page_to; $i++): ?>
                        <li class="page-item <?= $i == $pages->current ? 'disabled' : '' ?>"><a class="page-link" data-page="<?= $i ?>" href="<?= hfyGetLinkWithPage($pages, $i) ?>"><?= $i ?></a></li>
                        <?php
                    endfor;
                    ?>

                    <?php if ($pages->current + 2 < $pages->total): ?>
                        <li class="page-item disabled">...</li>
                    <?php endif; ?>

                    <?php if ($pages->current + 1 < $pages->total): ?>
                        <li class="page-item"><a class="page-link" data-page="<?= $pages->total ?>" href="<?= hfyGetLinkWithPage($pages, $pages->total) ?>"><?= $pages->total ?></a></li>
                    <?php endif; ?>

                    <?php if ($pages->current < $pages->total): ?>
                        <li class="page-item"><a class="page-link" data-page="<?= $pages->current + 1 ?>" href="<?= hfyGetLinkWithPage($pages, $pages->current + 1) ?>"><?= __('Next', 'hostifybooking') ?></a></li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>
    </div>
<?php endif; ?>
