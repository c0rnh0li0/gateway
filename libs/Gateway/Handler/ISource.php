<?php

namespace Gateway\Handler;

/**
 * Sources being used when processing import/export via API
 * and setting them to input of specific handlers.
 * 
 * @author Lukas Bruha
 */
interface ISource {

    const TYPE_FILE = 'file';
    const TYPE_FILEPATH = 'filepath';
    const TYPE_FILENAME = 'filename';
    const TYPE_TEXT = 'text';
    const TYPE_FOLDER = 'folder';
    
}

